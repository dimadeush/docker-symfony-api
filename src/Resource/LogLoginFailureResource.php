<?php
declare(strict_types = 1);
/**
 * /src/Resource/LogLoginFailureResource.php
 */

namespace App\Resource;

use App\DTO\Interfaces\RestDtoInterface;
use App\Entity\Interfaces\EntityInterface;
use App\Entity\LogLoginFailure as Entity;
use App\Entity\User;
use App\Repository\LogLoginFailureRepository as Repository;
use App\Rest\RestResource;

/**
 * Class LogLoginFailureResource
 *
 * @package App\Resource
 *
 * @codingStandardsIgnoreStart
 *
 * @method Entity getReference(string $id): Entity
 * @method Repository getRepository(): Repository
 * @method array<int, Entity> find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null): array
 * @method Entity|null findOne(string $id, ?bool $throwExceptionIfNotFound = null): ?EntityInterface
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?bool $throwExceptionIfNotFound = null): ?EntityInterface
 * @method Entity create(RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 * @method Entity update(string $id, RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 * @method Entity patch(string $id, RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 * @method Entity delete(string $id, ?bool $flush = null): EntityInterface
 * @method Entity save(EntityInterface $entity, ?bool $flush = null, ?bool $skipValidation = null): EntityInterface
 *
 * @codingStandardsIgnoreEnd
 */
class LogLoginFailureResource extends RestResource
{
    /**
     * Constructor
     */
    public function __construct(Repository $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * Method to reset specified user log login failures.
     */
    public function reset(User $user): void
    {
        $this->getRepository()->clear($user);
    }
}
