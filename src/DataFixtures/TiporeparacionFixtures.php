<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Tiporeparacion;

class TiporeparacionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $array=['Cambio de la fuente','Cambio de motherboard','Cambio de memoria RAM','Cambio  de HDD','Cambio de chasis'];
        foreach ($array as $value){
            $tipo=new Tiporeparacion();
            $tipo->setNombre($value);
            $manager->persist($tipo);
        }
        $manager->flush();

    }
}
