<?php

namespace App\Command;

use App\Entity\Autor;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NotificacionCommand extends Command
{
    protected static $defaultName = 'app:notificacion:delete-old';

    protected $manager;

    /**
     * NotificacionCommand constructor.
     * @param $manager
     */
    public function __construct(ObjectManager $manager = null)
    {
        $this->manager = $manager;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Elimina las notificaciones con 3 meses o más días antes del último login');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $fecha = new \DateTime('-3 months');
        $fecha = $fecha->format('d-m-Y H:i');
        $db = $this->manager->getConnection();
        $query = 'Delete from notificacion n where n.fecha < :fecha';
        $stmt = $db->prepare($query);
        $stmt->execute(['fecha' => $fecha]);
        $stmt->execute();

        $io->success('Las notificaciones antiguas finalizó exitosamente');
    }
}
