<?php
namespace App\Controller;

use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Livraison;


class FactureController extends AbstractController
{
    /**
     * @Route("/facture/{id}/pdf", name="facture_pdf")
     */
    public function generatePdfFacture(int $id): Response
    {
        // Récupérer les informations de la facture
        $livraison = $this->getDoctrine()->getRepository(Livraison::class)->findOneById($id);
        $user = $livraison->getUser();
        $paniers = $user->getPaniers();

        $produitPrixTotal = 0;
        if(is_array($paniers) || is_object($paniers)) {
            foreach ($paniers as $panier) {
                $produit = $panier->getProduit();
                $produitPrixTotal += $produit->getPrix();

            }

        }

        $total = $livraison->getTotal() ;

        $adresse = $livraison->getAdresse();
        $date_livraison = $livraison->getDateLivraison();

        // Générer le contenu du PDF
        $html = $this->renderView('facture/facture.html.twig', [
            'user' => $user,
            'paniers' => $paniers,
            'total' => $total,
            'adresse' => $adresse,
            'date_livraison' => $date_livraison,
        ]);

        // Générer le PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Renvoyer le PDF en réponse
        $pdfContent = $dompdf->output();
        return new Response($pdfContent, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="facture.pdf"',
        ]);
    }}