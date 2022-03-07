<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Like;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/addlike/{id}', name: 'addlike')]
    public function addLike(Film $film, LikeRepository $repository, EntityManagerInterface $manager)
    {

        $like = $repository->findOneBy(['user'=>$this->getUser(), 'film'=>$film]);



        if (!$like) {
            $like = new Like();
            $like->setFilm($film);
            $like->setUser($this->getUser());

            $manager->persist($like);
            $liked = true;
        } else {
            $manager->remove($like);
            $liked = false;
        }
        $manager->flush();

        return $this->redirectToRoute('film');

    }
}
