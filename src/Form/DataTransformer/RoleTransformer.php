<?php
declare(strict_types = 1);
/**
 * /src/Form/DataTransformer/RoleTransformer.php
 */

namespace App\Form\DataTransformer;

use App\Entity\Role;
use App\Resource\RoleResource;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Throwable;

/**
 * Class RoleTransformer
 *
 * @package App\Form\Console\DataTransformer
 */
class RoleTransformer implements DataTransformerInterface
{
    private RoleResource $resource;

    /**
     * Constructor
     */
    public function __construct(RoleResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Transforms an object (Role) to a string (Role id).
     *
     * @param Role|mixed $role
     */
    public function transform($role): string
    {
        return $role instanceof Role ? $role->getId() : '';
    }

    /**
     * Transforms a string (Role id) to an object (Role).
     *
     * @param string|mixed $roleName
     *
     * @throws TransformationFailedException if object (issue) is not found
     * @throws Throwable
     */
    public function reverseTransform($roleName): ?Role
    {
        $role = null;

        if ($roleName !== null) {
            $role = $this->resource->findOne((string)$roleName, false);

            if ($role === null) {
                throw new TransformationFailedException(sprintf(
                    'Role with name "%s" does not exist!',
                    (string)$roleName
                ));
            }
        }

        return $role;
    }
}
