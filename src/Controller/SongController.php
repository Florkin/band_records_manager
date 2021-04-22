<?php

namespace App\Controller;

use App\Entity\Song;
use App\Form\SongType;
use App\Repository\SongRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/song")
 */
class SongController extends AbstractController
{
    /**
     * @var SongRepository
     */
    private $songRepository;
    private $serializer;

    /**
     * SongController constructor.
     * @param SongRepository $songRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(SongRepository $songRepository, SerializerInterface $serializer)
    {
        $this->songRepository = $songRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="song_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            'song/index.html.twig',
            [
                'songs' => $this->songRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="song_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($song);
            $entityManager->flush();

            return $this->redirectToRoute('song_index');
        }

        return $this->render(
            'song/new.html.twig',
            [
                'song' => $song,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="song_show", methods={"GET"})
     * @param Song $song
     * @return Response
     */
    public function show(Song $song): Response
    {
        return $this->render(
            'song/show.html.twig',
            [
                'song' => $song,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="song_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Song $song
     * @return Response
     */
    public function edit(Request $request, Song $song): Response
    {
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('song_index');
        }

        return $this->render(
            'song/edit.html.twig',
            [
                'song' => $song,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="song_delete", methods={"POST"})
     * @param Request $request
     * @param Song $song
     * @return Response
     */
    public function delete(Request $request, Song $song): Response
    {
        if ($this->isCsrfTokenValid('delete'.$song->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($song);
            $entityManager->flush();
        }

        return $this->redirectToRoute('song_index');
    }

    /**
     * @Route("/{id}/records", name="song_get_records", methods={"GET"})
     * @param Song $song
     * @return Response
     */
    public function ajaxGetRecords(Song $song)
    {
        $context = SerializationContext::create()->setGroups('recordList');
        $records = [];
        foreach ($song->getRecords() as $record) {
            $records[$record->getId()] = $record;
        }

        $json = $this->serializer->serialize($records, 'json', $context);

        return new JsonResponse($json);
    }
}
