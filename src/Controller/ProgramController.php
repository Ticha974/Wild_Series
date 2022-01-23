<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Actor;
use App\Repository\ActorRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\ProgramType;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/program", name="program_")
 */

class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
        );
    }


    /**
     * Getting a program by id
     *
     * @Route("/show/{id<^[0-9]+$>}", name="show")
     * @ParamConverter("id", class="App\Entity\Program", options={"mapping": {"id": "id"}})
     * @return Response
     */
    public function show(Program $id, ProgramRepository $programRepo, SeasonRepository $seasonRepo, Program $program):Response
        {
            $seasons = $seasonRepo->findBy(['Program' => $program], ['number' => 'ASC']);

            if (!$program) {
                throw $this->createNotFoundException(
                    'No program with id : '.$id.' found in program\'s table.'
                );
            }
            return $this->render('program/show.html.twig', [
                'program' => $program,
                'seasons' => $seasons,
            ]);
        }

        /**
         * @Route("/{programId}/season/{seasonId}", name="season_show")
         * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
         * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
         * @return Response
         */
        public function showSeason(Program $program, Season $season, ProgramRepository $programRepo, SeasonRepository $seasonRepo, EpisodeRepository $episodeRepo):Response
        {
            $episodes = $episodeRepo->findBy(['season' => $season]);

            if (!$season) {
                throw $this->createNotFoundException(
                    'No season found in season\'s table.'
                );
            }
            return $this->render('program/season_show.html.twig', [
                'program' => $program,
                'season' => $season,
                'episodes' => $episodes,
            ]);
        }

        /**
         * @Route("/{programId}/season/{seasonId}/episode/{episodeId}", name="episode_show")
         * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
         * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
         * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeId": "id"}})
         * @return Response
         */
        public function showEpisode(Program $program, Season $season, Episode $episode, ProgramRepository $programRepo, SeasonRepository $seasonRepo, EpisodeRepository $episodeRepo):Response
        {

            if (!$episode) {
                throw $this->createNotFoundException(
                    'No episode found in episode\'s table.'
                );
            }
            return $this->render('program/episode_show.html.twig', [
                'program' => $program,
                'season' => $season,
                'episode' => $episode,
            ]);
        }

    /**
     * The controller for the category add form
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request) : Response
    {
        // Create a new Program Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data
            // Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();
            // Persist Category Object
            $entityManager->persist($program);
            // Flush the persisted object
            $entityManager->flush();
            // Finally redirect to categories list
            return $this->redirectToRoute('program_index');
        }
        return $this->render('program/new.html.twig', ["form" => $form->createView()]);
    }
    }
