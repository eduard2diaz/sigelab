<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Propiedad;

class PropiedadFixtures extends  Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $array=['IP privada','IP publica','MAC','Modelo','Fabricante','No. serie','No Inv.'];
        foreach ($array as $value){
           $propiedad=new Propiedad();
            $propiedad->setNombre($value);
           $manager->persist($propiedad);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }

}
