<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Occurrence;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/{slug}', name: 'app_api')]
    public function index(Request $request, string $slug, EntityManagerInterface $em): JsonResponse
    {
        $criteria = ['slug' => $slug];
        if ($calendar_ids = $request->query->get('calendars')) {
            $criteria['id'] = explode(',', $calendar_ids);
        }

        $calendars = $em->getRepository(Calendar::class)->findBy($criteria);

        if (empty($calendars)) {
            throw new NotFoundHttpException();
        }

        $startDate = new DateTimeImmutable($request->query->get('start'));
        $endDate   = new DateTimeImmutable($request->query->get('end'));
        /** @var Occurrence[] $occurrences */
        $occurrences = $em->getRepository(Occurrence::class)
            ->findBetweenDates($startDate, $endDate, $calendars);

        $payload = [];
        foreach ($occurrences as $occurrence) {
            $payload[] = [
                'id' => $occurrence->getId(),
                'title' => $occurrence->getTitle(),
                'start' => $occurrence->getStart()->format('c'),
                'end'   => $occurrence->getEnd()->format('c'),
                'description' => $occurrence->getDescription(),
                'event' => [
                    'calendar' => $occurrence->getEvent()->getCalendar()->getTitle(),
                    'calendar_slug' => $occurrence->getEvent()->getCalendar()->getSlug(),
                    'uid' => $occurrence->getEvent()->getUid(),
                    'rrule' => $occurrence->getEvent()->getRrule(),
                ],
            ];
        }

        return new JsonResponse($payload);
    }
}
