<?php
declare(strict_types=1);

namespace App\Command;

use App\Admin\ApiResource\Admin;
use App\Common\Security\RolesInterface;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Constraints\Length as LengthConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAdminUserCommand extends Command
{
    /**
     * @var int
     */
    public const MIN_PASSWORD_LENGTH = 7;

    /**
     * @var string
     */
    protected static $defaultName = 'admin:create';

    private EntityManagerInterface $entityManager;

    private PasswordHasher $passwordHasher;

    private ValidatorInterface $validator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param PasswordHasher $passwordHasher
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $entityManager, PasswordHasher $passwordHasher, ValidatorInterface $validator)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $emailConstraint = new EmailConstraint();
        $email = $helper->ask($input, $output, new Question("\n\nCreate user Admin.\nEmail: "));
        if ($email === null || \count($this->validator->validate($email, $emailConstraint)) > 0) {
            $output->writeln("<error>Invalid email</error>");

            return Command::INVALID;
        }

        $lengthConstraint = new LengthConstraint(null, self::MIN_PASSWORD_LENGTH);
        $pwQuestion = new Question('Password: ');
        $pwQuestion->setHidden(true);
        $password = $helper->ask($input, $output, $pwQuestion);
        if ($password === null || \count($this->validator->validate($password, $lengthConstraint))) {
            $output->writeln(\sprintf(
                "<error>Invalid password (Length should be greater than %s)</error>",
                self::MIN_PASSWORD_LENGTH
            ));

            return Command::INVALID;
        }

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Email($email),
            $this->passwordHasher->hash($password),
            'token'
        );

        $user->confirmSignUp();

        $user->changeRole(Role::admin());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io = new SymfonyStyle($input, $output);
        $io->success('User admin has been successfully created');

        return Command::SUCCESS;
    }


}