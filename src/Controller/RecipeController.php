<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    #[Route(path: '/api/image/{img}', name: 'image', methods: ['POST'])]
    public function image(Request $request, string $img): Response
    {
        // Vérifier si le dossier de destination existe sinon le créer
        $dossierDestination = $this->getParameter('kernel.project_dir') . '/assets/img/';
        // Récupérer le fichier image depuis la requête
        $fichierImage = $request->files->get('image');
        // Déplacer le fichier vers le dossier de destination avec le nom spécifié
        $fichierImage->move($dossierDestination, $img);
        // Répondre avec un message de succès
        return new Response('Image enregistrée avec succès', Response::HTTP_OK);
    }

    #[Route(path: '/api/getImage/{img}', name: 'getImage', methods: ['GET'])]
    public function getImage(string $img): BinaryFileResponse
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/assets/img/' . $img;

        // Create a BinaryFileResponse for the file
        $response = new BinaryFileResponse($filePath);

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $img);

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/jpeg');

        return $response;
    }
}
