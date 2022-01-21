<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @return Response
     */
    public function show(int $id, ProgramRepository $programRepo, SeasonRepository $seasonRepo):Response
        {
            $program = $programRepo->findOneBy(['id' => $id]);
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
     * @return Response
     */
    public function showSeason(Program $programId, Season $seasonId, ProgramRepository $programRepo, SeasonRepository $seasonRepo, EpisodeRepository $episodeRepo):Response
    {
        $program = $programRepo->findOneBy(['id' => $programId]);
        $season = $seasonRepo->findOneBy(['id' => $seasonId]);
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
    }
