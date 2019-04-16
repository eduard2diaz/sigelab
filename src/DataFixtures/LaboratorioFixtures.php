<?php

namespace App\DataFixtures;

use App\Entity\Laboratorio;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LaboratorioFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $facultad = $manager->getRepository('App:Facultad')->findOneByNombre('Facultad de Informatica');
        $array = ['101', '102', '103'];
        foreach ($array as $value) {
            $laboratorio = new Laboratorio();
            $laboratorio->setNombre($value);
            $laboratorio->setFacultad($facultad);
            $facultad->addIdlaboratorio($laboratorio);
            $laboratorio->setEnfuncionamiento(true);
            $manager->persist($facultad);
            $manager->persist($laboratorio);
        }
        $manager->flush();

    }

    public function getOrder()
    {
        return 2;
    }
}
