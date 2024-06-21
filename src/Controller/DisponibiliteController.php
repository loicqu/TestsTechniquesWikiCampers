<?php

namespace App\Controller;

use App\Entity\Disponibilite;
use App\Entity\DisponibiliteSearch;
use App\Form\DisponibiliteSearchType;
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
    public function lesDisponibilite(DisponibiliteRepository $disponibiliteRepository, Request $request): Response
    {
        $search = new DisponibiliteSearch(); #création d'un objet de la classe DisponibiliteSearch
        $form = $this->createForm(DisponibiliteSearchType::class, $search); # initialisation du formulaire
        $form->handleRequest($request);
        $valForm = $form->getData(); # récupération des valeurs données dans le formulaire
        if($valForm->getDateDebut() || $valForm->getDateFin() || $valForm->getPrixMax()){ # on vérifie qu'au moins un champ du formulaire n'est pas vide
            if($form->isSubmitted() && $form->isValid()){ # vérification de la validité du formulaire et on vérifie également qu'il a été soumis
                $lesDisponibilites = $disponibiliteRepository->searchDispo($search); # on effectue une recherche avec les valeurs
                if(!$lesDisponibilites and ($valForm->getDateDebut() || $valForm->getDateFin())){ # si le résultat est null et qu'au moins 1 date a été donné alors
                    if($valForm->getDateDebut()){
                        $valForm->setDateDebut($valForm->getDateDebut()->modify('-1 day')); #on fait -1 à la date de début
                    }
                    if($valForm->getDateFin()){
                        $valForm->setDateFin($valForm->getDateFin()->modify('+1 day')); # on fait -1 à la date de début
                    }
                    $lesDisponibilites = $disponibiliteRepository->searchDispo($search); # on effectue une deuxième recherche avec les nouvelles valeurs
                }
            }
        }
        else{
            $lesDisponibilites = $disponibiliteRepository->touteLesDisponibilites(); # si le formulaire n'est pas valide ou n'a pas été soumis, on renvoie toutes les disponibilités
        }
        return $this->render('disponibilite/index.html.twig',[
            'lesDisponibilites'=>$lesDisponibilites,
            'form' =>$form->createView(),
        ]
    );
    }

    #[Route('/les-disponibilite-admin', name: 'afficherLesDisponibilitesAdmin')]
    public function lesDisponibiliteAdmin(DisponibiliteRepository $disponibiliteRepository, Request $request): Response
    {
        $lesDisponibilites = $disponibiliteRepository->findAll(); # récupération des disponibilités 
        return $this->render('disponibilite/adminDisponibilite.html.twig',[
            'lesDisponibilites'=>$lesDisponibilites,
        ]
    );
    }

    #[Route('/ajouter-une-disponibilite', name: 'disponibilite_ajout')]
    #[Route('/modifier-un-disponibilite/{id}', name: 'disponibilite_modification')]
    public function AjoutEtModif(Disponibilite $disponibilite=null, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$disponibilite){ # si l'objet de la classe Disponibilite est null alors ça veut dire que l'action est l'ajout donc on en créé un vide
            $disponibilite = new Disponibilite();
        }
        $form = $this->createForm(DisponibiliteType::class, $disponibilite); # initialisation du formulaire
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ # on vérifie la validité du formulaire et on vérifie également qu'il a été soumis
            $manager->persist($disponibilite); # on enregistre 
            $manager->flush(); # et on envoie les valeurs dans la base (soit un INSERT soit un UPDATE)
            return $this->redirectToRoute("afficherLesDisponibilitesAdmin");
        }
        return $this->render('disponibilite/page-modif-ajout.html.twig', [
            'laDisponibilite' => $disponibilite,
            'form' => $form->createView(), 
        "isModification" => $disponibilite->getId()!==null]);
    }

    #[Route('/supprimer-dispo/{id}', name: 'disponibilite_suppression', methods:"delete")]
    public function suppression(Disponibilite $disponibilite=null, Request $request, EntityManagerInterface $manager): Response
    {
        if($this->isCsrfTokenValid("sup".$disponibilite->getId(), $request->get('_token'))){ # on vérifie que le token transmis soit le bon
            $manager->remove($disponibilite); # on supprime la ligne qui correspond à l'objet de la classe Disponibilite
            $manager->flush(); # on enregistre la modification
            $this->addFlash("success", "Suppression effectuée"); # on ajoute un addFlash pour pouvoir indiquer si l'action s'est bien passé ou non
             
        }
        else{
            $this->addFlash("failed", "Suppression non effectuée");
        }
        return $this->redirectToRoute("afficherLesDisponibilitesAdmin");      
    }
}
