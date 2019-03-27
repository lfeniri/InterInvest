<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'legal_forms' => [
                "Entrepreneur individuel",
                "Groupement de droit privé non doté de la personnalité morale",
                "Indivision",
                "Indivision entre personnes physiques",
                "Indivision avec personne morale",
                "Société créée de fait",
                "Société créée de fait entre personnes physiques",
                "Société créée de fait avec personne morale",
                "Société en participation",
                "Société en participation entre personnes physiques",
                "Société en participation avec personne morale",
                "Société en participation de professions libérales",
                "Fiducie",
                "Paroisse hors zone concordataire",
                "Autre groupement de droit privé non doté de la personnalité morale",
                "Personne morale de droit étranger",
                "Personne morale de droit étranger, immatriculée au RCS (registre du commerce et dessociétés)",
            ],
        ]);
    }
}
