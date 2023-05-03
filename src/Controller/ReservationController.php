<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Reservation;
use App\Entity\User;
use App\Form\ReservationType;
use App\Repository\EvenementRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use DateTime;
use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Description\Location;
use Jsvrcek\ICS\Utility\Formatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Part\DataPart;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/", name="app_reservation_index", methods={"GET"})
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new ", name="app_reservation_new", methods={"GET", "POST"})
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function new(Request $request,
                        ReservationRepository $reservationRepository,
                        UserRepository $userRepository,
                        MailerInterface $mailer,
                        EvenementRepository $evenementRepository
    ): Response {
        $reservation = new Reservation();
        $evenement = new Evenement();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $connectedUser = $userRepository->findOneById(FrontController::connectedUserId);
            $reservation->setUser($connectedUser);
            $reservation->setUser($connectedUser);
            $evenement_id = $form->getData()->evenement->getId();
            $evenement_from_database = $evenementRepository->findOneById($evenement_id);
            $reservationRepository->add($reservation, true);
            $this->sendEmail($evenement_from_database, $mailer, $connectedUser);
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_reservation_show", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_reservation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservationRepository->add($reservation, true);

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_reservation_delete", methods={"POST"})
     */
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $reservationRepository->remove($reservation, true);
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param $evenement_from_database
     * @param MailerInterface $mailer
     * @return void
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function sendEmail(Evenement $evenement_from_database, MailerInterface $mailer, User $connectedUser): void
    {
        $email = (new Email())
            ->from('antika.pidev.symfony@gmail.com')
            ->to($connectedUser->getEmail())
            ->subject('You participated in ' . $evenement_from_database->getNom() . "!");

        $event = new CalendarEvent();
        $startDateTime = new \DateTime('@'.$evenement_from_database->getEvenementDate()->getTimestamp(), $evenement_from_database->getEvenementDate()->getTimezone());
        $endDateTime = clone $startDateTime;
        $endDateTime->add(\DateInterval::createFromDateString('2 hours'));
        $event
            ->setStart($startDateTime)
            ->setSummary($evenement_from_database->getNom())
            ->setUid('event-uid')
            ->setEnd($endDateTime);
        $location = new Location();
        $location->setUri($evenement_from_database->getLieu());
        $event->addLocation($location);
        $calendar = new Calendar();
        $calendar->setProdId('Nada PI DEV Symfony')->addEvent($event);
        $calendar->setMethod('REQUEST');
        $calendarExport = new CalendarExport(new CalendarStream(), new Formatter());
        $calendarExport->addCalendar($calendar);
        $ics = $calendarExport->getStream();

        $attachment = new DataPart($ics, 'inline.ics', 'text/calendar', 'quoted-printable');
        $attachment->asInline();
        $attachment->getHeaders()->addParameterizedHeader('Content-Type', 'text/calendar', ['charset' => 'utf-8', 'method' => 'REQUEST']);
        $email->attachPart($attachment);
        $email->getHeaders()->remove("Content-Type");
        $email->getHeaders()->addTextHeader('MIME-Version', '1.0')->addTextHeader('Content-Type', 'text/calendar');

        $mailer->send($email);
    }
}
