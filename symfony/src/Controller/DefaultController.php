<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Model\Day;
use App\Model\Event;
use App\Entity\Occurrence;
use App\Repository\DayRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route("/", name: "home")]
    public function home(): Response
    {
        return $this->redirectToRoute('calendars');
    }

    #[Route("/calendars", name: "calendars")]
    public function calendars(EntityManagerInterface $em): Response
    {
        $calendars = $em->getRepository(Calendar::class)->findAll();

        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendars,
        ]);
    }

    #[Route("/calendar/{slug}", name: "calendar")]
    public function calendar(string $slug) : Response
    {
        return $this->redirectToRoute('agenda', ['slug' => $slug]);
    }

    #[Route("/calendar/{slug}/week", name: "week")]
    public function week(Request $request, string $slug, EntityManagerInterface $em): Response
    {
        $calendar = $em->getRepository(Calendar::class)->findOneBy(['slug' => $slug]);
        if (!$calendar) {
            throw new NotFoundHttpException();
        }

        $page      = intval($request->query->get('page', 0));
        $baseDate  = $this->baseDate($page);
        $startDate = $baseDate->modify('last Sunday');
        $endDate   = $baseDate->modify('next Sunday');

        $occurrences = $em->getRepository(Occurrence::class)
            ->findBetweenDates($startDate, $endDate, $calendar);

        $dayRepo = new DayRepository();
        foreach ($occurrences as $occurrence) {
            $day = $dayRepo->getDay($occurrence->getStart()) ?: new Day($occurrence->getStart());
            $day->addOccurrence($occurrence);
            $dayRepo->addDay($day);
        }

        $dayRepo->fillDates($startDate, $endDate);

        return $this->render('calendar/week.html.twig', [
            'repo' => $dayRepo,
        ]);
    }

    #[Route("/calendar/{slug}/month", name: "month")]
    public function month(Request $request, string $slug, EntityManagerInterface $em): Response
    {
        $calendar = $em->getRepository(Calendar::class)->findOneBy(['slug' => $slug]);
        if (!$calendar) {
            throw new NotFoundHttpException();
        }

        $page      = intval($request->query->get('page', 0));
        $baseDate  = $this->baseDate($page);
        $startDate = $baseDate->modify('first day of this month')->modify('last Sunday');
        $endDate   = $baseDate->modify('last day of this month')->modify('next Sunday');

        $occurrences = $em->getRepository(Occurrence::class)
            ->findBetweenDates($startDate, $endDate, $calendar);

        $dayRepo = new DayRepository();
        foreach ($occurrences as $occurrence) {
            $day = $dayRepo->getDay($occurrence->getStart()) ?: new Day($occurrence->getStart());
            $day->addOccurrence($occurrence);
            $dayRepo->addDay($day);
        }

        $dayRepo->fillDates($startDate, $endDate);

        return $this->render('calendar/month.html.twig', [
            'repo' => $dayRepo,
        ]);
    }

    #[Route("/calendar/{slug}/agenda", name: "agenda")]
    public function agenda(Request $request, string $slug, EntityManagerInterface $em) : Response
    {
        $calendar = $em->getRepository(Calendar::class)->findOneBy(['slug' => $slug]);
        if (!$calendar) {
            throw new NotFoundHttpException();
        }

        $page = intval($request->query->get('page', 0));
        $baseDate = $this->baseDate($page);

        $occurrences = $em->getRepository(Occurrence::class)->findBetweenDates(
            $baseDate->modify('first day of this month'),
            $baseDate->modify('first day of next month'),
            $calendar
        );

        return $this->render('calendar/agenda.html.twig', [
            'events' => $occurrences,
        ]);
    }

    /**
     * Render a pager.
     *
     * @param RequestStack $stack
     * @return Response
     */
    public function pager(RequestStack $stack): Response
    {
        $page = $stack->getMainRequest()->query->get('page', 0);
        $params = $stack->getMainRequest()->get('_route_params');
        $route = $stack->getMainRequest()->get('_route');

        return $this->render('calendar/_pager.html.twig', [
            'current_page' => $page,
            'route' => $route,
            'params' => $params,
        ]);
    }

    /**
     * Render tabs menu.
     *
     * @param RequestStack $stack
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function tabs(RequestStack $stack, EntityManagerInterface $em): Response
    {

        $slug = $stack->getMainRequest()->get('_route_params')['slug'];
        $route = $stack->getMainRequest()->get('_route');
        $page = $stack->getMainRequest()->query->get('page', 0);
        $baseDate = $this->baseDate($page);

        $items = [
            'agenda' => 'Agenda',
            'month' => 'Month',
//            'week' => 'Week',
        ];

        return $this->render('calendar/_tabs.html.twig', [
            'items' => $items,
            'slug' => $slug,
            'current_route' => $route,
            'base_date' => $baseDate,
        ]);
    }

    /**
     * @param int $page
     * @return DateTimeImmutable
     */
    private function baseDate(int $page): DateTimeImmutable
    {
        return (new DateTimeImmutable(date('Y').'-'.date('m').'-01'))->add(DateInterval::createFromDateString("$page months"));
    }
}
