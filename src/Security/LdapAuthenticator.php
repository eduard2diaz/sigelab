<?php

namespace App\Security;

use App\Entity\Facultad;
use App\Entity\Rol;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use App\Services\LdapService;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class LdapAuthenticator extends AbstractGuardAuthenticator
{
    private $repository;
    private $ldapservice;
    private $encoder;
    private $em;

    public function __construct(UsuarioRepository $repository, EntityManager $em, LdapService $ldapservice, UserPasswordEncoderInterface $encoder)
    {
        $this->repository = $repository;
        $this->ldapservice = $ldapservice;
        $this->encoder = $encoder;
        $this->em = $em;
    }

    public function supports(Request $request)
    {
        return $request->request->has('_username') && $request->request->has('_password');
    }

    public function getCredentials(Request $request)
    {
        if (!$request->request->has('_username') || !$request->request->has('_password')) {
            return;
        }

        $credentials = [
            '_username' => $request->request->get('_username'),
            '_password' => $request->request->get('_password')
        ];
        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->repository->loadUserByUsername($credentials['_username']);

        if (!$user) {
            $login_status = $this->ldapservice->login($credentials['_username'], $credentials['_password']);
            if (!$login_status)
                return;
            else {
                $search = $this->ldapservice->search($credentials['_username']);
                $search = json_decode($search);
                if (isset($search->error))
                    return;

                $usuario = new Usuario();
                $usuario->setNombre($search->nombre);
                $usuario->setApellido($search->apellido);
                $usuario->setCorreo($search->correo);
                $usuario->setUsuario($search->usuario);
                $usuario->setPassword($credentials['_password']);
                $facultad = $search->facultad;
                $facultadObj = $this->em->getRepository(Facultad::class)->findOneByNombre($facultad);

                if (!$facultadObj) {
                    $facultadObj = new Facultad();
                    $facultadObj->setNombre($facultad);
                    $this->em->persist($facultadObj);
                    $this->em->flush();
                }
                $usuario->setFacultad($facultadObj);

                $rol = $search->rol;
                if ($rol == 'Profesores')
                    $rol = "ROLE_PROFESOR";
                else
                    $rol = "ROLE_ESTUDIANTE";
                $rol = $this->em->getRepository(Rol::class)->findOneByNombre($rol);

                $usuario->addIdrol($rol);
                $this->em->persist($facultadObj);
                $this->em->flush();

                return $usuario;
            }
        }


        if ($this->encoder->isPasswordValid($user, $credentials['_password'])) {
            return $user;
        }else{
            $login_status = $this->ldapservice->login($credentials['_username'], $credentials['_password']);
            if (!$login_status)
                return;
            else
                return $user;
        }

        return;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
