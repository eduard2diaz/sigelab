<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\Common\Persistence\ObjectManager;

class ReservacionlaboratorioDeleteCommand extends Command
{
    protected static $defaultName = 'app:reservacionlaboratorio:delete';
    protected $manager;

    public function __construct(ObjectManager $manager = null)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $fecha=new \DateTime('-1 year');
        $fecha=$fecha->format('d-m-Y H:i');
        $db = $this->manager->getConnection();
        $query = 'Delete from reservacionlaboratorio n where n.fechainicio < :fecha';
        $stmt = $db->prepare($query);
        $stmt->execute(['fecha'=>$fecha]);

        $io->success('las reservaciones de laboratorio fueron eliminadas exitosamente.');
    }
}
