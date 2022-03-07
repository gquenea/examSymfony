<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\ImpressionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImpressionController extends AbstractController
{
    /**
     * @Route("/impression/new/{id}", name="newimpression")
     * @param Film $film
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newImpression(Film $film, Request $request, EntityManagerInterface $manager){

            $impression = new Impression();

            $form = $this->createForm(ImpressionType::class, $impression);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid() && $impression->getUser() == $this->getUser()) {
                $impression->setUser($this->getUser());
                $impression->setFilm($film);
                $impression->setDate(new \DateTime());
                $manager->persist($impression);
                $manager->flush();
            }
            return $this->redirectToRoute('showfilm', ['id'=>$film->getId()]);
    }


    /**
     * @Route("/impression/delete/{id}", name="deleteimp", priority=1)
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteImpression(Impression $impression,EntityManagerInterface $manager){

        $id = $impression->getFilm()->getId();

        if ($impression && $impression->getUser() == $this->getUser()){
            $manager->remove($impression);
            $manager->flush();
        }
        return$this->redirectToRoute('showfilm',['id'=>$id]);
    }


    /**
     * @Route("/impression/modify/{id}", name="modifyimp")
     * @param Impression $impression
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function modifyImp(Impression $impression,Request $request, EntityManagerInterface $manager){

            $form = $this->createForm(ImpressionType::class,$impression);
            $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $impression->getUser() == $this->getUser()) {
            $id = $impression->getFilm()->getId();
            $impression = $form->getData();
            $manager->persist($impression);
            $manager->flush();

            return $this->redirectToRoute('showfilm', ['id'=>$id]);
        }
        return $this->renderForm('impression/modify.html.twig', ['form'=>$form]);
    }
}
