<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Periferico;

class PerifericoFixtures extends  Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $propiedades=['Modelo','Fabricante','No. serie','No Inv.'];
        $perifericos=[
            ['nombre'=>'Teclado','tipo'=>'in'],
            ['nombre'=>'Mouse','tipo'=>'in'],
            ['nombre'=>'Monitor','tipo'=>'out'],
            ['nombre'=>'Bocina','tipo'=>'out'],
            ['nombre'=>'Impresora','tipo'=>'out'],
            ['nombre'=>'Scanner','tipo'=>'in'],
            ];

        foreach ($perifericos as $perifericos_it){
           $periferico=new Periferico();
            $periferico->setNombre($perifericos_it['nombre']);
            $periferico->setTipo($perifericos_it['tipo']);
            foreach ($propiedades as $prop){
                $propObj=$manager->getRepository('App:Propiedad')->findOneBy(array('nombre'=>$prop));
                if(null!=$propObj)
                $periferico->addIdpropiedad($propObj);
            }
           $manager->persist($periferico);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }

}
