<?php

namespace PiedWeb\CMSBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use PiedWeb\CMSBundle\EventListener\MediaCacheGeneratorTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserCommand extends Command
{
    use MediaCacheGeneratorTrait;

    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $userClass;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoder $passwordEncoder,
        $userClass
    ) {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->userClass = $userClass;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Create a new user')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('role', InputArgument::REQUIRED);
    }

    protected function getMedias(InputInterface $input)
    {
        $repo = $this->em->getRepository($this->params->get('pwc.entity_media'));

        if ($input->getArgument('media')) {
            return $repo->findBy(['media' => $input->getArgument('media')]);
        }

        return $repo->findAll();
    }

    protected function createUser($email, $password, $role)
    {
        $userClass = $this->userClass;
        $user = new $userClass();
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
        $user->setRoles([$role]);

        $this->em->persist($user);
        $this->em->flush();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createUser($input->getArgument('email'), $input->getArgument('password'), $input->getArgument('role'));

        $output->writeln('<info>done...</info>');
    }
}
