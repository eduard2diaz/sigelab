<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/requesttoken", name="api_requesttoken")
     * Este servicio se puede consumir utilizando curl u otro cliente url como Guzzle, por ejemplo
     * curl -X POST --data "username=administrador&password=administrador" http://localhost/rody/public/index.php/api/requesttoken
     */
    public function requestToken(Request $request, UserPasswordEncoderInterface $encoder)
    {
        if ($request->request->has('username') && $password = $request->request->has('password')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $em = $this->getDoctrine()->getManager();
            $usuario = $em->getRepository(Usuario::class)->findOneByUsuario($username);
            if (!$usuario)
                return $this->json(['message' => 'Invalid credentials'], 401);

            $password = $encoder->encodePassword($usuario, $password);
            if ($encoder->isPasswordValid($usuario, $password))
                return $this->json(['message' => 'Invalid credentials'], 401);

            $token = $em->getRepository(ApiToken::class)->findOneByUsuario($usuario);
            if (!$token) {
                $token = new ApiToken($usuario);
            } else {
                if ($token->isExpired())
                    $token->renewToken();
                $token->renewExpiresAt();
            }

            $em->persist($token);
            $em->flush();
            return $this->json([
                'token' => $token->getToken(),
                'expiresAt' => $token->getExpiresAt()->format('d-m-Y H:i'),
            ], 200);
        }
        return $this->json(['message' => 'Authentication Required'], 401);
    }

    /**
     * @Route("/reservacionlaboratorio/index", name="api_reservacion_laboratorio_index")
     * Estos servicios se pueden consumir pasandole el token generado por el metodo anterior y la url a consumir
     * curl -H "X-AUTH-TOKEN: a3d2b49855a63c72357e04b1caa77f23e77781711688c1a55826e7d61430c66c82ca0acab7fa3ea8f8a3bdb47522895f9882c6c68fa556b6058b148f"
     * http://localhost/rody/public/index.php/api/reservacionlaboratorio/index
     */
    public function reservacionLaboratorioIndex()
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $consulta = $em->createQuery('SELECT r FROM App:ReservacionLaboratorio r JOIN r.usuario a WHERE a.id= :id');
        $consulta->setParameter('id', $usuario->getId());
        $reservaciones = $consulta->getResult();

        $result=[];
        foreach ($reservaciones as $value)
            $result[]=['fechainicio'=>$value->getFechainicio()->format('d-m-Y H:i'),'fechafin'=>$value->getFechafin()->format('d-m-Y H:i'),'laboratorio'=>$value->getLaboratorio()->getNombre(),'facultad'=>$value->getLaboratorio()->getFacultad()->getNombre()];

        return $this->json(['reservaciones' => $result], 200);
    }

    /**
     * @Route("/reservacionpc/index", name="api_reservacion_pc_index")
     * Estos servicios se pueden consumir pasandole el token generado por el metodo anterior y la url a consumir
     * curl -H "X-AUTH-TOKEN: 2411b06c62eae35822e06d0de36af40c354a64083c09c3e0f103a7912a638f4d9325531640bbc973ee3920125606cd321c057838cc3cc893a25f8381"
     * http://localhost/rody/public/index.php/api/reservacionpc/index
     */
    public function reservacionPcIndex()
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $consulta = $em->createQuery('SELECT r FROM App:ReservacionPc r JOIN r.usuario a WHERE a.id= :id');
        $consulta->setParameter('id', $usuario->getId());
        $reservaciones = $consulta->getResult();

        $result=[];
        foreach ($reservaciones as $value)
            $result[]=['fecha'=>$value->getFecha()->format('d-m-Y H:i'),'pc'=>$value->getPc()->getNumero(),'laboratorio'=>$value->getLaboratorio()->getNombre(),'facultad'=>$value->getLaboratorio()->getFacultad()->getNombre()];

        return $this->json(['reservaciones' => $result], 200);
    }

}
