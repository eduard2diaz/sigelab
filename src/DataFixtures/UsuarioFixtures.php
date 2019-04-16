<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Usuario;

class UsuarioFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $usuario=new Usuario();
        $usuario->setNombre('Administrador');
        $usuario->setApellido('Administrador');
        $usuario->setUsuario('administrador');
        $usuario->setCorreo('administrador@unah.edu.cu');
        $facultad='Facultad de Informatica';
        $facultad = $manager->getRepository('App:Facultad')->findOneBy(array(
            'nombre' => $facultad
        ));
        $usuario->setFacultad($facultad);
        $usuario->setActivo(true);
        $usuario->setPassword('administrador');
        $usuario->setSalt(base64_encode($usuario->getPassword()));
        $rol = $manager->getRepository('App:Rol')->findOneBy(array(
            'nombre' => 'ROLE_ADMIN'
        ));
        $usuario->getIdrol()->add($rol);
        $manager->persist($usuario);
        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
