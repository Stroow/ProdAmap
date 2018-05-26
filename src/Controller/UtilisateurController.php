<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Producteur;
use App\Form\UtilisateurType;
use App\Entity\producteurUtilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/utilisateur")
 */
class UtilisateurController extends Controller
{
    /**
     * @Route("/", name="utilisateur_index", methods="GET")
     */
    public function index(): Response
    {
        $utilisateurs = $this->getDoctrine()
        ->getRepository(Utilisateur::class)
        ->findAll();

        return $this->render('utilisateur/index.html.twig', ['utilisateurs' => $utilisateurs]);
    }

    /**
     * @Route("/new", name="utilisateur_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($utilisateur);
            $em->flush();

            return $this->redirectToRoute('utilisateur_index');
        }

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{utilId}", name="utilisateur_show", methods="GET")
     */
    public function show(Utilisateur $utilisateur): Response
    {
        $unProducteur = new producteurUtilisateur();

        $unProducteur->setUtilID($utilisateur->getutilID());        
        $unProducteur->setName($utilisateur->getutilNom());
        $unProducteur->setPhone($utilisateur->getutilTel());

        if ($utilisateur->getProducteur()!=NULL) {

            $producteur = self::getProducteurProfil($utilisateur);
            $unProducteur->setNamePro($producteur->getProdNomExploit());
        }
        return $this->render('utilisateur/show.html.twig', ['utilisateur' => $unProducteur]);
    }

    /**
     * @Route("/{utilId}/edit", name="utilisateur_edit", methods="GET|POST")
     */
    public function edit(Request $request, Utilisateur $utilisateur): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('utilisateur_edit', ['utilId' => $utilisateur->getUtilId()]);
        }

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{utilId}", name="utilisateur_delete", methods="DELETE")
     */
    public function delete(Request $request, Utilisateur $utilisateur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getUtilId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($utilisateur);
            $em->flush();
        }

        return $this->redirectToRoute('utilisateur_index');
    }

    public function getProducteurProfil($utilisateur){
      $producteur = $this->getDoctrine()
      ->getRepository(Producteur::class)
      ->find($utilisateur->getProducteur());

      return $producteur;

  }
}