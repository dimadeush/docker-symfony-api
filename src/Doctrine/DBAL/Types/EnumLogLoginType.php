<?php
declare(strict_types = 1);
/**
 * /src/Doctrine/DBAL/Types/EnumLogLoginType.php
 */

namespace App\Doctrine\DBAL\Types;

/**
 * Class EnumLogLoginType
 *
 * @package App\Doctrine\DBAL\Types
 */
class EnumLogLoginType extends EnumType
{
    public const TYPE_FAILURE = 'failure';
    public const TYPE_SUCCESS = 'success';

    protected static string $name = 'EnumLogLogin';
    protected static array $values = [
        self::TYPE_FAILURE,
        self::TYPE_SUCCESS,
    ];
}
