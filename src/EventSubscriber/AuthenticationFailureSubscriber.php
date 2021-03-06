<?php
declare(strict_types = 1);
/**
 * /src/EventSubscriber/AuthenticationFailureSubscriber.php
 */

namespace App\EventSubscriber;

use App\Doctrine\DBAL\Types\EnumLogLoginType;
use App\Repository\UserRepository;
use App\Service\LoginLoggerService;
use Doctrine\ORM\ORMException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

/**
 * Class AuthenticationFailureSubscriber
 *
 * @package App\EventSubscriber
 */
class AuthenticationFailureSubscriber implements EventSubscriberInterface
{
    private LoginLoggerService $loginLoggerService;
    private UserRepository $userRepository;

    /**
     * Constructor
     */
    public function __construct(LoginLoggerService $loginLoggerService, UserRepository $userRepository)
    {
        $this->loginLoggerService = $loginLoggerService;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationFailureEvent::class => 'onAuthenticationFailure',
        ];
    }

    /**
     * Method to log login failures to database.
     *
     * This method is called when following event is broadcast;
     *  - \Lexik\Bundle\JWTAuthenticationBundle\Events::AUTHENTICATION_FAILURE
     *
     * @throws ORMException|Throwable
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $token = $event->getException()->getToken();

        // Fetch user entity
        if ($token !== null && is_string($token->getUser())) {
            /** @var string $username */
            $username = $token->getUser();
            $this->loginLoggerService->setUser($this->userRepository->loadUserByUsername($username, false));
        }

        $this->loginLoggerService->process(EnumLogLoginType::TYPE_FAILURE);
    }
}
