<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Methods/FindMethod.php
 */

namespace App\Rest\Traits\Methods;

use App\Rest\RequestHandler;
use App\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait FindMethod
 *
 * @package App\Rest\Traits\Methods
 *
 * @method ResponseHandler getResponseHandler()
 */
trait FindMethod
{
    /**
     * Generic 'findMethod' method for REST resources.
     *
     * @param string[]|null $allowedHttpMethods
     *
     * @throws Throwable
     */
    public function findMethod(Request $request, ?array $allowedHttpMethods = null): Response
    {
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? ['GET']);
        // Determine used parameters
        $orderBy = RequestHandler::getOrderBy($request);
        $limit = RequestHandler::getLimit($request);
        $offset = RequestHandler::getOffset($request);
        $search = RequestHandler::getSearchTerms($request);

        try {
            $criteria = RequestHandler::getCriteria($request);
            $this->processCriteria($criteria, $request, __METHOD__);

            return $this
                ->getResponseHandler()
                ->createResponse($request, $resource->find($criteria, $orderBy, $limit, $offset, $search), $resource);
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception);
        }
    }
}
