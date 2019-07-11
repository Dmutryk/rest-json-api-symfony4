<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    private $entityManager;
    private $userPasswordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    protected function configure()
    {
        $this
            ->setName('app:create-user')
            ->setDescription('Creates a user entry in database.')
            ->addOption(
                'username',
                null,
                InputOption::VALUE_REQUIRED,
                'The username of the user.'
            )
            ->addOption(
                'email',
                null,
                InputOption::VALUE_REQUIRED,
                'The email of the user.'
            )
            ->addOption(
                'roles',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'The roles of the user.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $password = bin2hex(random_bytes(16));

        $this->createUser($input, $password);
        $this->outputCredentials($output, $input->getOption('username'), $password);
    }

    private function createUser(InputInterface $input, $password)
    {
        $user = new User();
        $user->setUsername($input->getOption('username'));
        $user->setEmail($input->getOption('email'));
        $user->setRoles($input->getOption('roles'));
        $password = $this->userPasswordEncoder->encodePassword($user, $password);
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function outputCredentials(OutputInterface $output, $username, $password)
    {
        $output->writeln(PHP_EOL.'CREDENTIALS');
        $output->writeln('-----------');
        $output->writeln('Username: '.$username);
        $output->writeln('Password: '.$password.PHP_EOL);
    }
}
