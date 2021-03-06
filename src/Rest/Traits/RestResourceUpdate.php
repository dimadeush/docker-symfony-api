<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/RestResourceUpdate.php
 */

namespace App\Rest\Traits;

use App\DTO\Interfaces\RestDtoInterface;
use App\Entity\Interfaces\EntityInterface;

/**
 * Trait RestResourceUpdate
 *
 * @SuppressWarnings("unused")
 *
 * @package App\Rest\Traits
 */
trait RestResourceUpdate
{
    /**
     * Before lifecycle method for update method.
     */
    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
    }

    /**
     * After lifecycle method for update method.
     *
     * Notes: If you make changes to entity in this lifecycle method by default it will be saved on end of current
     *          request. To prevent this you need to detach current entity from entity manager.
     *
     *          Also note that if you've made some changes to entity and you eg. throw an exception within this method
     *          your entity will be saved if it has eg Blameable / Timestampable traits attached.
     */
    public function afterUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
    }
}
