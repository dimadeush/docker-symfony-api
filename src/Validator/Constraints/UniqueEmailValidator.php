<?php
declare(strict_types = 1);
/**
 * /src/App/Validator/Constraints/UniqueEmailValidator.php
 */

namespace App\Validator\Constraints;

use App\Entity\Interfaces\UserInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueEmailValidator
 *
 * @package App\Validator\Constraints
 */
class UniqueEmailValidator extends ConstraintValidator
{
    private UserRepository $repository;

    /**
     * Constructor
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NonUniqueResultException
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value instanceof UserInterface
            && !$this->repository->isEmailAvailable($value->getEmail(), $value->getId())
        ) {
            $this->context
                ->buildViolation(UniqueEmail::MESSAGE)
                ->setCode(UniqueEmail::IS_UNIQUE_EMAIL_ERROR)
                ->addViolation();
        }
    }
}
