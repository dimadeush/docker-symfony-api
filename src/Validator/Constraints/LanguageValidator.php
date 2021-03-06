<?php
declare(strict_types = 1);
/**
 * /src/Validator/Constraints/LanguageValidator.php
 */

namespace App\Validator\Constraints;

use App\Service\LocalizationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class LanguageValidator
 *
 * @package App\Validator\Constraints
 */
class LanguageValidator extends ConstraintValidator
{
    private LocalizationService $localization;

    /**
     * Constructor
     */
    public function __construct(LocalizationService $localization)
    {
        $this->localization = $localization;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (in_array($value, $this->localization->getLanguages(), true) !== true) {
            $this->context
                ->buildViolation(Language::MESSAGE)
                ->setParameter('{{ language }}', (string)$value)
                ->setCode(Language::INVALID_LANGUAGE)
                ->addViolation();
        }
    }
}
