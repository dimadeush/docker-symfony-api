<?php
declare(strict_types = 1);
/**
 * /src/Form/DataTransformer/UserGroupTransformer.php
 */

namespace App\Form\DataTransformer;

use App\Entity\UserGroup;
use App\Resource\UserGroupResource;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class UserGroupTransformer
 *
 * @package App\Form\Console\DataTransformer
 */
class UserGroupTransformer implements DataTransformerInterface
{
    private UserGroupResource $resource;

    /**
     * Constructor
     */
    public function __construct(UserGroupResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Transforms an object (Role) to a string (Role id).
     *
     * @param array<int, string|UserGroup>|mixed $userGroups
     *
     * @return array<int, string>
     *
     * @psalm-suppress MissingClosureParamType
     */
    public function transform($userGroups): array
    {
        $output = [];

        if (is_array($userGroups)) {
            $iterator = static fn ($group): string => $group instanceof UserGroup ? $group->getId() : (string)$group;

            $output = array_values(array_map('\strval', array_map($iterator, $userGroups)));
        }

        return $output;
    }

    /**
     * Transforms a string (Role id) to an object (Role).
     *
     * @param array<int, string>|mixed $userGroups
     *
     * @throws TransformationFailedException if object (issue) is not found
     *
     * @return array<int, UserGroup>|null
     */
    public function reverseTransform($userGroups): ?array
    {
        $output = null;

        if (is_array($userGroups)) {
            $iterator = function (string $groupId): UserGroup {
                /** @var UserGroup|null $group */
                $group = $this->resource->findOne($groupId);

                if ($group === null) {
                    throw new TransformationFailedException(sprintf(
                        'User group with id "%s" does not exist!',
                        $groupId
                    ));
                }

                return $group;
            };

            $output = array_values(array_map($iterator, $userGroups));
        }

        return $output;
    }
}
