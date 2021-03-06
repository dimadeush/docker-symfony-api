<?php
declare(strict_types = 1);
/**
 * /src/Command/User/RemoveUserGroupCommand.php
 */

namespace App\Command\User;

use App\Command\Traits\StyleSymfony;
use App\Entity\UserGroup;
use App\Resource\UserGroupResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class RemoveUserGroupCommand
 *
 * @package App\Command\User
 */
class RemoveUserGroupCommand extends Command
{
    // Traits
    use StyleSymfony;

    private UserGroupResource $userGroupResource;
    private UserHelper $userHelper;

    /**
     * Constructor
     *
     * @throws LogicException
     */
    public function __construct(UserGroupResource $userGroupResource, UserHelper $userHelper)
    {
        parent::__construct('user:remove-group');

        $this->userGroupResource = $userGroupResource;
        $this->userHelper = $userHelper;

        $this->setDescription('Console command to remove existing user group');
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getSymfonyStyle($input, $output);

        $userGroup = $this->userHelper->getUserGroup($io, 'Which user group you want to remove?');
        $message = null;

        if ($userGroup instanceof UserGroup) {
            // Delete user group
            $this->userGroupResource->delete($userGroup->getId());
            $message = 'User group removed - have a nice day';
        }

        if ($input->isInteractive()) {
            $message ??= 'Nothing changed - have a nice day';
            $io->success($message);
        }

        return 0;
    }
}
