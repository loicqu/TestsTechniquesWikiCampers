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

    #[Route('/les-vehicules-admin', name: 'afficherLesVehiculesAdmin')]
    public function lesVehiculesAdmin(VehiculeRepository $vehiculeRepository, Request $request): Response
    {
        $lesVehicules = $vehiculeRepository->findAll(); # on récupère tous les véhicules présents dans la base de données
        return $this->render('Vehicule/adminVehicule.html.twig',[
            'lesVehicules'=>$lesVehicules,
        ]
    );
    }

    #[Route('/les-vehicules', name: 'afficherLesVehicules')]
    public function lesVehicules(VehiculeRepository $vehiculeRepository, Request $request): Response
    {
        $lesVehicules = $vehiculeRepository->findAll(); # on récupère tous les véhicules présents dans la base de données
        return $this->render('Vehicule/index.html.twig',[
            'lesVehicules'=>$lesVehicules,
        ]
    );
    }

    #[Route('/ajouter-un-vehicule', name: 'vehicule_ajout')]
    #[Route('/modifier-un-vehicule/{id}', name: 'vehicule_modification')]
    public function AjoutEtModif(Vehicule $vehicule=null, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$vehicule){ # si l'objet de la classe Vehicule est null alors ça veut dire que l'action est l'ajout donc on en créé un vide
            $vehicule = new Vehicule();
        }
        $form = $this->createForm(VehiculeType::class, $vehicule); # initialisation du formulaire
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ # on vérifie la validité du formulaire et on vérifie également qu'il a été soumis
            $manager->persist($vehicule); # on enregistre 
            $manager->flush(); # et on envoie les valeurs dans la base (soit un INSERT soit un UPDATE)
            return $this->redirectToRoute("afficherLesVehiculesAdmin"); # on redirige l'utilisateur vers une autre page
        }
        return $this->render('vehicule/page-modif-ajout.html.twig', [
            'leVehicule' => $vehicule,
            'form' => $form->createView(), 
        "isModification" => $vehicule->getId()!==null]);
    }

    #[Route('/supprimer/{id}', name: 'vehicule_suppression', methods:"delete")]
    public function suppression(Vehicule $vehicule=null, Request $request, EntityManagerInterface $manager): Response
    {
        if($this->isCsrfTokenValid("sup".$vehicule->getId(), $request->get('_token'))){ # on vérifie que le token transmis soit le bon
            $manager->remove($vehicule); # on supprime la ligne qui correspond à l'objet de la classe Disponibilite
            $manager->flush(); # on enregistre la modification
            $this->addFlash("success", "Suppression effectuée"); # on ajoute un addFlash pour pouvoir indiquer si l'action s'est bien passé ou non
             
        }
        else{
            $this->addFlash("failed", "Suppression non effectuée");
        }
        return $this->redirectToRoute("afficherLesVehiculesAdmin");      
    }
}
