<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/MethodValidator.php
 */

namespace App\Rest\Traits;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\Interfaces\ControllerInterface;
use App\Rest\Traits\Methods\RestMethodProcessCriteria;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\Mapping\MappingException;
use Error;
use Exception;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use TypeError;
use UnexpectedValueException;

/**
 * Trait MethodValidator
 *
 * @package App\Rest\Traits
 */
trait RestMethodHelper
{
    use RestMethodProcessCriteria;

    /**
     * Method + DTO class names (key + value)
     *
     * @var array<string, string>
     */
    protected static array $dtoClasses = [];

    /**
     * Getter method for used DTO class for current controller.
     *
     * @throws UnexpectedValueException
     */
    public function getDtoClass(?string $method = null): string
    {
        $dtoClass = $method !== null && array_key_exists($method, static::$dtoClasses)
            ? static::$dtoClasses[$method]
            : $this->getResource()->getDtoClass();

        if (!in_array(RestDtoInterface::class, class_implements($dtoClass), true)) {
            $message = sprintf(
                'Given DTO class \'%s\' is not implementing \'%s\' interface.',
                $dtoClass,
                RestDtoInterface::class
            );

            throw new UnexpectedValueException($message);
        }

        return $dtoClass;
    }

    /**
     * Method to validate REST trait method.
     *
     * @throws LogicException
     * @throws MethodNotAllowedHttpException
     */
    public function validateRestMethod(Request $request, array $allowedHttpMethods): void
    {
        // Make sure that we have everything we need to make this work
        if (!($this instanceof ControllerInterface)) {
            $message = sprintf(
                'You cannot use \'%s\' controller class with REST traits if that does not implement \'%s\'',
                static::class,
                ControllerInterface::class
            );

            throw new LogicException($message);
        }

        if (!in_array($request->getMethod(), $allowedHttpMethods, true)) {
            throw new MethodNotAllowedHttpException($allowedHttpMethods);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleRestMethodException(Throwable $exception, ?string $id = null): Throwable
    {
        if ($id !== null) {
            $this->detachEntityFromManager($id);
        }

        return $this->determineOutputAndStatusCodeForRestMethodException($exception);
    }

    /**
     * Getter method for exception code with fallback to `400` bad response.
     */
    private function getExceptionCode(Throwable $exception): int
    {
        $code = (int)$exception->getCode();

        return $code !== 0 ? $code : Response::HTTP_BAD_REQUEST;
    }

    /**
     * Method to detach entity from entity manager so possible changes to it won't be saved.
     *
     * @throws MappingException
     */
    private function detachEntityFromManager(string $id): void
    {
        $currentResource = $this->getResource();
        $entityManager = $currentResource->getRepository()->getEntityManager();
        // Fetch entity
        $entity = $currentResource->getRepository()->find($id);

        // Detach entity from manager if it's been managed by it
        if ($entity !== null
            /* @scrutinizer ignore-call */
            && $entityManager->getUnitOfWork()->getEntityState($entity) === UnitOfWork::STATE_MANAGED
        ) {
            $entityManager->clear();
        }
    }

    /**
     * @param Throwable|Exception|TypeError|Error $exception
     */
    private function determineOutputAndStatusCodeForRestMethodException($exception): Throwable
    {
        $code = $this->getExceptionCode($exception);
        $output = new HttpException($code, $exception->getMessage(), $exception, [], $code);

        if ($exception instanceof HttpException) {
            $output = $exception;
        } elseif ($exception instanceof NoResultException) {
            $code = Response::HTTP_NOT_FOUND;
            $output = new HttpException($code, 'Not found', $exception, [], $code);
        } elseif ($exception instanceof NonUniqueResultException) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $output = new HttpException($code, $exception->getMessage(), $exception, [], $code);
        }

        return $output;
    }
}
