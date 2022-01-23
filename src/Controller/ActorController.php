<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Repository\ActorRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\ProgramType;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/actor", name="actor_")
 */

class ActorController extends AbstractController
{
    /**
     * Show all rows from Actor’s entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $actors = $this->getDoctrine()
            ->getRepository(Actor::class)
            ->findAll();

        return $this->render(
            'actor/index.html.twig',
            ['actors' => $actors]
        );
    }

    /**
     * @Route("/{id<^[0-9]+$>}", name="show")
     *  @ParamConverter("id", class="App\Entity\Actor", options={"mapping": {"id": "id"}})
     * @return Response A response instance
     */

    public function show(\Doctrine\Persistence\ManagerRegistry $doctrine, Actor $id): Response
    {
        $actor = $doctrine->getRepository(Actor::class)->findOneBy(['id' => $id]);
        $programs = $actor->getPrograms();

        if (!$actor) {
            throw $this->createNotFoundException(
                'No actor with id : '.$id.' found in actor\'s table.'
            );
        }
        return $this->render('actor/show.html.twig', [
            'actor' => $actor, 'programs' => $programs]);
    }
}
