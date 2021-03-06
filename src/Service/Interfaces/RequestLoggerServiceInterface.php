<?php
declare(strict_types = 1);
/**
 * /src/Service/Interfaces/RequestLoggerServiceInterface.php
 */

namespace App\Service\Interfaces;

use App\Entity\ApiKey;
use App\Entity\User;
use App\Resource\LogRequestResource;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface RequestLoggerServiceInterface
 *
 * @package App\Service\Interfaces
 */
interface RequestLoggerServiceInterface
{
    /**
     * Constructor
     *
     * @param array<int, string> $sensitiveProperties
     */
    public function __construct(LogRequestResource $resource, LoggerInterface $logger, array $sensitiveProperties);

    /**
     * Setter for response object.
     */
    public function setResponse(Response $response): self;

    /**
     * Setter for request object.
     */
    public function setRequest(Request $request): self;

    /**
     * Setter method for current user.
     */
    public function setUser(?User $user = null): self;

    /**
     * Setter method for current api key
     */
    public function setApiKey(?ApiKey $apiKey = null): self;

    /**
     * Setter method for 'master request' info.
     */
    public function setMasterRequest(bool $masterRequest): self;

    /**
     * Method to handle current response and log it to database.
     */
    public function handle(): void;
}
