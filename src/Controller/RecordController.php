<?php

namespace App\Controller;

use App\Entity\Record;
use App\Entity\Song;
use App\Form\RecordType;
use App\Repository\RecordRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/record")
 */
class RecordController extends AbstractController
{
    /**
     * @var RecordRepository
     */
    private $recordRepository;
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * RecordController constructor.
     * @param RecordRepository $recordRepository
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        RecordRepository $recordRepository,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $this->recordRepository = $recordRepository;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="record_index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $context = SerializationContext::create()->setGroups('recordList');
            $json = $this->serializer->serialize($this->recordRepository->findAll(), 'json', $context);

            return new JsonResponse($json);
        }

        return $this->render(
            'record/index.html.twig',
            [
                'records' => $this->recordRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="record_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $record = new Record();
        $form = $this->createForm(RecordType::class, $record);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($record);
            $entityManager->flush();

            return $this->redirectToRoute('record_index');
        }

        return $this->render(
            'record/new.html.twig',
            [
                'record' => $record,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="record_show", methods={"GET"})
     */
    public function show(Record $record): Response
    {
        return $this->render(
            'record/show.html.twig',
            [
                'record' => $record,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="record_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Record $record): Response
    {
        $form = $this->createForm(RecordType::class, $record);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('record_index');
        }

        return $this->render(
            'record/edit.html.twig',
            [
                'record' => $record,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="record_delete", methods={"POST"})
     * @param Request $request
     * @param Record $record
     * @return Response
     */
    public function delete(Request $request, Record $record): Response
    {
        if ($this->isCsrfTokenValid('delete'.$record->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($record);
            $entityManager->flush();
        }

        return $this->redirectToRoute('record_index');
    }

    /**
     * @Route("/record/add/{id}", name="add_record", methods={"POST"})
     * @param Request $request
     * @param Song|null $song
     * @return Response
     */
    public function ajaxAddRecord(Request $request, Song $song = null)
    {
        $file = $request->files->get('file');
        $record = new Record();
        $record->setSong($song);
        $record->setRecordFile($request->files->get('file'));
        $song->addRecord($record);
        $this->entityManager->flush();

        $context = SerializationContext::create()->setGroups('recordList');
        $jsonRecord = $this->serializer->serialize($record, 'json', $context);
        $template = $this->renderView('record/partials/_record.html.twig', ['record' => $record]);

        return new JsonResponse(['record' => $jsonRecord, 'template' => $template]);
    }
}
