<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Rol;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class RolFixtures extends  Fixture implements  OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $array=['ROLE_ESTUDIANTE','ROLE_ADMIN','ROLE_PROFESOR','ROLE_TECNICO','ROLE_JEFETECNICO'];
        foreach ($array as $value){
            $rol=new Rol();
            $rol->setNombre($value);
            $manager->persist($rol);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
