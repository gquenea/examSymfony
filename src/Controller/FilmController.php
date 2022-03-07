<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\FilmType;
use App\Form\ImpressionType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    #[Route('/', name: 'film')]
    public function index(FilmRepository $film): Response
    {
        return $this->render('film/index.html.twig', [
            'films'=>$film->findAll(),
        ]);
    }

    /**
     * @Route("/unfilm/{id}", name="showfilm")
     * @param Film $film
     * @return Response
     */
    public function show(Film $film){

        $impression = new Impression();

        $formimp = $this->createForm(ImpressionType::class, $impression);

        return $this->renderForm('film/show.html.twig', ['film'=>$film,'formimp'=>$formimp]);
    }


    /**
     * @Route("film/new", name="newfilm")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newFilm(Request $request, EntityManagerInterface $manager) {

        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $film->getUser() == $this->getUser()){
            $film->setUser($this->getUser());
            $manager->persist($film);
            $manager->flush();

            return $this->redirectToRoute('film');
        }

        return $this->renderForm('film/new.html.twig', ['form'=>$form]);


    }

    /**
     * @Route("/film/delete/{id}", name="deletefilm")
     * @param Film $film
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteFilm(Film $film, EntityManagerInterface $manager) {

        if ($film && $film->getUser() == $this->getUser()) {
            $manager->remove($film);
            $manager->flush();
        }
            return $this->redirectToRoute('film');
    }


    /**
     * @Route("/film/modify/{id}", name="modifyfilm")
     * @param Film $film
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function modify(Film $film,Request $request, EntityManagerInterface $manager) {

        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $film->getUser() == $this->getUser()){
            $film = $form->getData();
            $manager->persist($film);
            $manager->flush();

            return $this->redirectToRoute('film');
        }
        return $this->renderForm('film/new.html.twig', ['form'=>$form]);
    }
}
