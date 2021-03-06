<?php
declare(strict_types = 1);
/**
 * /src/Repository/BaseRepository.php
 */

namespace App\Repository;

use App\Entity\Interfaces\EntityInterface;
use App\Repository\Interfaces\BaseRepositoryInterface;
use App\Repository\Traits\RepositoryMethods;
use App\Repository\Traits\RepositoryWrappers;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class BaseRepository
 *
 * @package App\Repository
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    // Traits
    use RepositoryMethods;
    use RepositoryWrappers;

    private const INNER_JOIN = 'innerJoin';
    private const LEFT_JOIN = 'leftJoin';

    /**
     * @var array<int, string>
     */
    protected static array $searchColumns = [];
    protected static string $entityName;
    protected static EntityManager $entityManager;

    /**
     * Joins that need to attach to queries, this is needed for to prevent duplicate joins on those.
     *
     * @var array<string, array<int, array<int, array<int, string>>>>
     */
    private static array $joins = [
        self::INNER_JOIN => [],
        self::LEFT_JOIN => [],
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private static array $processedJoins = [
        self::INNER_JOIN => [],
        self::LEFT_JOIN => [],
    ];

    /**
     * @var array<int, array<int, callable|mixed>>
     */
    private static array $callbacks = [];

    /**
     * @var array<int, string>
     */
    private static array $processedCallbacks = [];

    /**
     * Constructor
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityName(): string
    {
        return static::$entityName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchColumns(): array
    {
        return static::$searchColumns;
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface $entity, ?bool $flush = null): self
    {
        $flush ??= true;
        // Persist on database
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(EntityInterface $entity, ?bool $flush = null): self
    {
        $flush ??= true;
        // Remove from database
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function processQueryBuilder(QueryBuilder $queryBuilder): void
    {
        // Reset processed joins and callbacks
        self::$processedJoins = [self::INNER_JOIN => [], self::LEFT_JOIN => []];
        self::$processedCallbacks = [];
        $this->processJoins($queryBuilder);
        $this->processCallbacks($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function addLeftJoin(array $parameters): self
    {
        if (count($parameters) > 1) {
            $this->addJoinToQuery(self::LEFT_JOIN, $parameters);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addInnerJoin(array $parameters): self
    {
        if (count($parameters) > 0) {
            $this->addJoinToQuery(self::INNER_JOIN, $parameters);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addCallback(callable $callable, ?array $args = null): self
    {
        $args ??= [];
        $hash = sha1(serialize(array_merge([spl_object_hash((object)$callable)], $args)));

        if (!in_array($hash, self::$processedCallbacks, true)) {
            self::$callbacks[] = [$callable, $args];
            self::$processedCallbacks[] = $hash;
        }

        return $this;
    }

    /**
     * Process defined joins for current QueryBuilder instance.
     */
    protected function processJoins(QueryBuilder $queryBuilder): void
    {
        foreach (self::$joins as $joinType => $joins) {
            foreach ($joins as $joinParameters) {
                $queryBuilder->{$joinType}(...$joinParameters);
            }

            self::$joins[$joinType] = [];
        }
    }

    /**
     * Process defined callbacks for current QueryBuilder instance.
     *
     * @psalm-suppress PossiblyInvalidArgument
     */
    protected function processCallbacks(QueryBuilder $queryBuilder): void
    {
        foreach (self::$callbacks as [$callback, $args]) {
            array_unshift($args, $queryBuilder);
            $callback(...$args);
        }

        self::$callbacks = [];
    }

    /**
     * Method to add defined join(s) to current QueryBuilder query. This will keep track of attached join(s) so any of
     * those are not added multiple times to QueryBuilder.
     *
     * @note processJoins() method must be called for joins to actually be added to QueryBuilder. processQueryBuilder()
     *       method calls this method automatically.
     *
     * @see QueryBuilder::leftJoin()
     * @see QueryBuilder::innerJoin()
     *
     * @param string $type Join type; leftJoin, innerJoin or join
     * @param array<int, array<int, string>> $parameters Query builder join parameters
     */
    private function addJoinToQuery(string $type, array $parameters): void
    {
        $comparison = implode('|', $parameters);

        if (!in_array($comparison, self::$processedJoins[$type], true)) {
            self::$joins[$type][] = $parameters;

            self::$processedJoins[$type][] = $comparison;
        }
    }
}
