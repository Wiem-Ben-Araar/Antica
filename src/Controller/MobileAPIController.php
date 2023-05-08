<?php


namespace App\Controller;

use App\Repository\EvenementRepository;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Evenement;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Json;


/**
 * @Route("/mobile")
 */
class MobileAPIController extends  AbstractController
{

    /**
     * @Route("/addEvent", name="add_event")
     * @Method("POST")
     */

    public function addEvent(Request $request, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $evenement = new Evenement();

        $evenement->setNom($request->get('nom'));
        $evenement->setLieu($request->get('lieu'));
        $evenement->setDescription($request->get('description'));
        $evenement->setCapacite($request->get('capacite'));
        $em->persist($evenement);
        $em->flush();

        $jsonContent = $normalizer->normalize($evenement,'json',['groups'=>"evenements"]);
        return new Response(json_encode($jsonContent));

    }

    /**
     * @Route("/deleteEvent/{id}", name="delete_event")
     * @Method("DELETE")
     */

    public function deleteEvent(Request $request, $id, NormalizerInterface $normalizer) {

        $em = $this->getDoctrine()->getManager();
        $evenement = $em->getRepository(Evenement::class)->find($id);
        if($evenement!=null ) {
            $em->remove($evenement);
            $em->flush();

            $jsonContent = $normalizer->normalize($evenement, 'json',['groups'=>"evenements"]);
            return new Response("Event was deleted successfully.".json_encode($jsonContent));


        }
        return new JsonResponse("id evenement invalide.");


    }

    /**
     * @Route("/updateEvent/{id}", name="update_event")
     * @Method("PUT")
     */
    public function modifierEventAction(Request $request, $id , NormalizerInterface $normalizer) {
        $em = $this->getDoctrine()->getManager();
        $evenement = $em->getRepository(Evenement::class)->find($id);

        $evenement->setNom($request->get("nom"));
        $evenement->setLieu($request->get("lieu"));
        $evenement->setDescription($request->get("description"));
        $evenement->setCapacite($request->get("capacite"));

        $em->flush();
        $jsonContent = $normalizer->normalize($evenement,'json',['groups'=>"evenements"]);
        return new Response("Event was updated successfully.".json_encode($jsonContent));

    }

    /**
     * @Route("/displayEvents", name="display_events")
     */
    public function allEventAction(EvenementRepository $evenementRepository , NormalizerInterface $normalizer):Response
    {

        $evenements = $evenementRepository->findAll();
        $evenementsNormalises = $normalizer->normalize($evenements, 'json', ['groups' => "evenements"]);
        $json = json_encode($evenementsNormalises);

        return new Response($json);

    }

    /**
     * @Route("/detailEvent/{id}", name="detail_event")
     * @Method("GET")
     */
    public function detailEventAction($id, NormalizerInterface $normalizer, EvenementRepository $evenementRepository)
    {
        $evenement = $evenementRepository->find($id);
        $evenementNormalises = $normalizer->normalize($evenement, 'json', ['groups' =>"evenements"]);
        return new Response(json_encode($evenementNormalises));
    }

}