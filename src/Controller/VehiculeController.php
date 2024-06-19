<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\BienRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class VehiculeController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(): Response
    {
        return $this->render('index.html.twig'
    );
    }

    #[Route('/les-vehicules', name: 'afficherLesVehicules')]
    public function lesVehicules(VehiculeRepository $vehiculeRepository, Request $request): Response
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
        $lesVehicules = $vehiculeRepository->findAll();
        return $this->render('Vehicule/index.html.twig',[
            'lesVehicules'=>$lesVehicules,
            #'form' =>$form->createView(),
        ]
    );
    }

    #[Route('/ajouter-un-vehicule', name: 'vehicule_ajout')]
    #[Route('/modifier-un-vehicule/{id}', name: 'vehicule_modification')]
    public function AjoutEtModif(Vehicule $vehicule=null, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$vehicule){
            $vehicule = new Vehicule();
        }
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($vehicule);
            $manager->flush();
            return $this->redirectToRoute("afficherLesVehicules");
        }
        return $this->render('vehicule/page-modif-ajout.html.twig', [
            'leVehicule' => $vehicule,
            'form' => $form->createView(), 
        "isModification" => $vehicule->getId()!==null]);
    }

    #[Route('/supprimer/{id}', name: 'vehicule_suppression', methods:"delete")]
    public function suppression(Vehicule $vehicule=null, Request $request, EntityManagerInterface $manager): Response
    {
        if($this->isCsrfTokenValid("sup".$vehicule->getId(), $request->get('_token'))){
            $manager->remove($vehicule);
            $manager->flush();
            $this->addFlash("success", "Suppression effectuée");
             
        }
        else{
            $this->addFlash("failed", "Suppression non effectuée");
        }
        return $this->redirectToRoute("afficherLesVehicules");      
    }
}
