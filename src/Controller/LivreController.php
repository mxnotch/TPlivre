<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Entity\Categorie;
use App\Entity\Auteur;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AuteurRepository; // Importez AuteurRepository
use Doctrine\Persistence\ManagerRegistry;


#[Route('/livre')]
class LivreController extends AbstractController
{

    #[Route('/nombre-emprunts', name: 'app_livre_nombre_emprunts', methods: ['GET'])]
    public function nombreEmpruntsParLivre(LivreRepository $livreRepository): Response
    {
        $resultats = $livreRepository->getNombreEmpruntsParLivre();

        return $this->render('livre/nombre_emprunts.html.twig', [
            'resultats' => $resultats,
        ]);
    }

    #[Route('/emprunts-en-cours', name: 'app_livre_emprunts_en_cours', methods: ['GET'])]
public function empruntsEnCours(LivreRepository $livreRepository): Response
{
    $livres = $livreRepository->findLivresEmpruntes();

    return $this->render('livre/emprunts_en_cours.html.twig', [
        'livres' => $livres,
    ]);
}


    #[Route('/auteur/{id}', name: 'app_livre_by_auteur', methods: ['GET'])]
public function findByAuteur(Auteur $auteur, LivreRepository $livreRepository): Response
{
    $livres = $livreRepository->findByAuteur($auteur);

    return $this->render('livre/by_auteur.html.twig', [
        'livres' => $livres,
        'auteur' => $auteur,
    ]);
}




    #[Route('/', name: 'app_livre_index', methods: ['GET'])]
    public function index(LivreRepository $livreRepository): Response
    {
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_livre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivreRepository $livreRepository): Response
    {
        $livre = new Livre();
        $livre->setDesactive(false);
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les catégories sélectionnées
            $categories = $form->get('categories')->getData();
            foreach ($categories as $categorie) {
                $livre->addCategorie($categorie);
            }

            $livreRepository->save($livre, true);

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livre $livre, LivreRepository $livreRepository): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les catégories sélectionnées
            $categories = $form->get('categories')->getData();
            $livre->getCategories()->clear();
            foreach ($categories as $categorie) {
                $livre->addCategorie($categorie);
            }

            $livreRepository->save($livre, true);

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, LivreRepository $livreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $livre->setDesactive(1);
            $livre->getCategories()->clear();
            $livreRepository->save($livre, true);
        }

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }
}
