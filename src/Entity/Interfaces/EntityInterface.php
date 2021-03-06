<?php
declare(strict_types = 1);
/**
 * /src/Entity/Interfaces/EntityInterface.php
 */

namespace App\Entity\Interfaces;

use DateTimeImmutable;

/**
 * Interface EntityInterface
 *
 * @package App\Entity\Interfaces
 */
interface EntityInterface
{
    public function getId(): string;

    public function getCreatedAt(): ?DateTimeImmutable;
}
