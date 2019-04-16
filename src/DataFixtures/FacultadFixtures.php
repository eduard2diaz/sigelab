<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Facultad;

class FacultadFixtures extends  Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $array=['Facultad de Ciencias Tecnicas','Facultad de Derecho','Facultad de Informatica','Facultad de veterinaria'];
        foreach ($array as $value){
           $facultad=new Facultad();
           $facultad->setNombre($value);
           $manager->persist($facultad);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}
