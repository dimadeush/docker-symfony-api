<?php
declare(strict_types = 1);
/**
 * /src/Repository/RoleRepository.php
 */

namespace App\Repository;

use App\Entity\Role as Entity;

/**
 * Class RoleRepository
 *
 * @package App\Repository
 *
 * @codingStandardsIgnoreStart
 *
 * @method Entity|null find(string $id, ?int $lockMode = null, ?int $lockVersion = null): ?Entity
 * @method array<int, Entity> findAdvanced(string $id, $hydrationMode = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null): ?Entity
 * @method array<int, Entity> findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
 * @method array<int, Entity> findByAdvanced(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null): array
 * @method array<int, Entity> findAll(): array
 *
 * @codingStandardsIgnoreEnd
 */
class RoleRepository extends BaseRepository
{
    protected static string $entityName = Entity::class;
}
