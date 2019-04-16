<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Pieza;

class PiezaFixtures extends  Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $propiedades=['Modelo','Fabricante','No. serie','No Inv.'];
        $piezas=['Tarjeta de red', 'Tarjeta de video','RAM', 'Micro','Board','Tarjeta de sonido'];

        foreach ($piezas as $piezas_it){
           $pieza=new Pieza();
            $pieza->setNombre($piezas_it);
            foreach ($propiedades as $prop){
                $propObj=$manager->getRepository('App:Propiedad')->findOneBy(array('nombre'=>$prop));
                if(null!=$propObj)
                $pieza->addIdpropiedad($propObj);
            }
           $manager->persist($pieza);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 7;
    }

}
