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
            ['nombre'=>'Teclado','tipo'=>'Entrada'],
            ['nombre'=>'Mouse','tipo'=>'Entrada'],
            ['nombre'=>'Monitor','tipo'=>'Salida'],
            ['nombre'=>'Bocina','tipo'=>'Salida'],
            ['nombre'=>'Impresora','tipo'=>'Salida'],
            ['nombre'=>'Scanner','tipo'=>'Entrada'],
            ['nombre'=>'HDD','tipo'=>'Almacenamiento'],
            ['nombre'=>'Disco externo','tipo'=>'Almacenamiento'],
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
