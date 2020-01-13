<?php
declare(strict_types = 1);
/**
 * /src/Command/ApiKey/EditApiKeyCommand.php
 */

namespace App\Command\ApiKey;

use App\Command\Traits\StyleSymfony;
use App\DTO\ApiKey\ApiKey as ApiKeyDto;
use App\Entity\ApiKey as ApiKeyEntity;
use App\Form\Type\Console\ApiKeyType;
use App\Resource\ApiKeyResource;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class EditApiKeyCommand
 *
 * @package App\Command\ApiKey
 */
class EditApiKeyCommand extends Command
{
    // Traits
    use StyleSymfony;

    private ApiKeyResource $apiKeyResource;
    private ApiKeyHelper $apiKeyHelper;

    /**
     * Constructor
     *
     * @param ApiKeyResource $apiKeyResource
     * @param ApiKeyHelper   $apiKeyHelper
     *
     * @throws LogicException
     */
    public function __construct(ApiKeyResource $apiKeyResource, ApiKeyHelper $apiKeyHelper)
    {
        parent::__construct('api-key:edit');

        $this->apiKeyResource = $apiKeyResource;
        $this->apiKeyHelper = $apiKeyHelper;

        $this->setDescription('Command to edit existing API key');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Throwable
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);
        // Get API key entity
        $apiKey = $this->apiKeyHelper->getApiKey($io, 'Which API key you want to edit?');
        $message = null;

        if ($apiKey instanceof ApiKeyEntity) {
            $message = $this->updateApiKey($input, $output, $apiKey);
        }

        if ($input->isInteractive()) {
            $message ??= 'Nothing changed - have a nice day';
            $io->success($message);
        }

        return 0;
    }

    /**
     * Method to update specified API key via specified form.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param ApiKeyEntity    $apiKey
     *
     * @throws Throwable
     *
     * @return array
     */
    private function updateApiKey(InputInterface $input, OutputInterface $output, ApiKeyEntity $apiKey): array
    {
        // Load entity to DTO
        $dtoLoaded = new ApiKeyDto();
        $dtoLoaded->load($apiKey);
        /** @var FormHelper $helper */
        $helper = $this->getHelper('form');
        /** @var ApiKeyDto $dtoEdit */
        $dtoEdit = $helper->interactUsingForm(ApiKeyType::class, $input, $output, ['data' => $dtoLoaded]);
        // Update API key
        $this->apiKeyResource->update($apiKey->getId(), $dtoEdit);

        return $this->apiKeyHelper->getApiKeyMessage('API key updated - have a nice day', $apiKey);
    }
}