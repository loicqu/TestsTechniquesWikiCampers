<?php

namespace App\Controller;

use App\Entity\Disponibilite;
use App\Form\DisponibiliteType;
use App\Repository\DisponibiliteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DisponibiliteController extends AbstractController
{
    #[Route('/les-disponibilite', name: 'afficherLesDisponibilites')]
    public function lesVehicules(DisponibiliteRepository $disponibiliteRepository, Request $request): Response
    {
        #$search = new BienSearch();
        #$form = $this->createForm(BienSearchType::class, $search);
        #$form->handleRequest($request);
        #$valForm = $form->getData();
        /* if($valForm->getPrixMax() || $valForm->getSurface() || $valForm->getVille()){
            if($form->isSubmitted() && $form->isValid()){
                $bien = $bienRepository->searchBien($search);
            }
        }
        else{
            $bien = $bienRepository->findAll();
        } */
        $lesDisponibilites = $disponibiliteRepository->touteLesDisponibilites();
        return $this->render('disponibilite/index.html.twig',[
            'lesDisponibilites'=>$lesDisponibilites,
            #'form' =>$form->createView(),
        ]
    );
    }

    #[Route('/ajouter-une-disponibilite', name: 'disponibilite_ajout')]
    public function AjoutEtModif(Disponibilite $disponibilite=null, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$disponibilite){
            $disponibilite = new Disponibilite();
        }
        $form = $this->createForm(DisponibiliteType::class, $disponibilite);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($disponibilite);
            $manager->flush();
            return $this->redirectToRoute("afficherLesDisponibilites");
        }
        return $this->render('vehicule/page-modif-ajout.html.twig', [
            'laDisponibilite' => $disponibilite,
            'form' => $form->createView(), 
        "isModification" => $disponibilite->getId()!==null]);
    }
}
