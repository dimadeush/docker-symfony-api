<?php
declare(strict_types = 1);
/**
 * /src/Service/LoginLoggerService.php
 */

namespace App\Service;

use App\Entity\LogLogin;
use App\Entity\User;
use App\Resource\LogLoginResource;
use App\Service\Interfaces\LoginLoggerServiceInterface;
use BadMethodCallException;
use DeviceDetector\DeviceDetector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;

/**
 * Class LoginLoggerService
 *
 * @package App\Service
 */
class LoginLoggerService implements LoginLoggerServiceInterface
{
    private LogLoginResource $logLoginResource;
    private RequestStack $requestStack;
    private ?User $user = null;
    private DeviceDetector $deviceDetector;

    /**
     * Constructor
     */
    public function __construct(LogLoginResource $logLoginResource, RequestStack $requestStack)
    {
        // Store used services
        $this->logLoginResource = $logLoginResource;
        $this->requestStack = $requestStack;
        $this->deviceDetector = new DeviceDetector();
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(?User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function process(string $type): void
    {
        // Get current request
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new BadMethodCallException('Could not get request from current request stack');
        }

        // Parse user agent data with device detector
        $this->deviceDetector = new DeviceDetector((string)$request->headers->get('User-Agent', ''));
        $this->deviceDetector->parse();
        // Create entry
        $this->createEntry($type, $request);
    }

    /**
     * Method to create new login entry and store it to database.
     *
     * @throws Throwable
     */
    private function createEntry(string $type, Request $request): void
    {
        $entry = new LogLogin($type, $request, $this->deviceDetector, $this->user);
        // And store entry to database
        $this->logLoginResource->save($entry, true);
    }
}
