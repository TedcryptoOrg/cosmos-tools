<?php

declare(strict_types=1);

namespace App\Controller\Cosmos;

use App\Controller\BaseController;
use App\Form\Cosmos\SignerFormHandler;
use App\Model\UpgradeWatcher\UpgradeWatcher;
use App\Service\Polkachu\PolkachuApiClient;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpgradesController extends BaseController
{
    public function __construct(
        private readonly UpgradeWatcher $upgradeWatcher,
        private readonly PolkachuApiClient $polkachuApiClient
    ) {
    }

    #[Route(path: '/cosmos/upgrades', name: 'app_cosmos_upgrades_index')]
    public function indexAction(Request $request, SignerFormHandler $signerFormHandler): Response
    {
        $formResponse = $this->createAndHandleFormHandler($signerFormHandler, $request);

        return $this->render(
            'cosmos/upgrades/index.html.twig',
            [
                'form' => $formResponse->getForm(),
            ]
        );
    }

    #[Route(path: '/cosmos/upgrades/ical.ics', name: 'app_cosmos_upgrades_ical')]
    public function icalAction(Request $request): Response
    {
        return $this->polkachuIcalAction($request);
    }

    #[Route(path: '/cosmos/defiantlabs/upgrades/ical.ics', name: 'app_defiantlabs_cosmos_upgrades_ical')]
    public function defiantLabIcalAction(Request $request): Response
    {
        $upgrades = $this->upgradeWatcher->getUpgrades();
        $chains = $request->query->all('chains');
        $events = [];
        foreach ($upgrades as $upgrade) {
            if ($chains && !\in_array($upgrade->network, $chains, true)) {
                continue;
            }

            $timeSpan = new TimeSpan(
                new DateTime($upgrade->estimatedUpgradeTime, true),
                new DateTime($upgrade->estimatedUpgradeTime->modify('+10 minutes'), true)
            );
            $description = 'Network: '.$upgrade->network.
                '<br>Chain: '.$upgrade->chainName.
                '<br>Version: '.$upgrade->version.
                '<br>Estimated upgrade time: '.$upgrade->estimatedUpgradeTime->format('Y-m-d H:i:s').
                '<br>Block: '.$upgrade->upgradeBlockHeight;
            $event = new Event();
            $event
                ->setSummary('Upgrade '.$upgrade->chainName.' to '.$upgrade->version)
                ->setOccurrence($timeSpan)
                ->setDescription($description)
            ;
            $events[] = $event;
        }

        if ($request->query->has('view')) {
            return $this->json(\array_map(
                fn (Event $event) => [
                    'title' => $event->getSummary(),
                    'start' => $event->getOccurrence()->getBegin(),
                    'end' => $event->getOccurrence()->getEnd(),
                    'description' => $event->getDescription(),
                ],
                $events
            ));
        }

        $calendar = new Calendar($events);

        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);

        $calendarResponse = new Response((string) $calendarComponent);
        $calendarResponse->headers->set('Content-Type', 'text/calendar; charset=utf-8');
        $calendarResponse->headers->set('Content-Disposition', 'attachment; filename="calendar.ics"');

        return $calendarResponse;
    }

    #[Route(path: '/cosmos/polkachu/upgrades/ical.ics', name: 'app_polkachu_cosmos_upgrades_ical')]
    public function polkachuIcalAction(Request $request): Response
    {
        $cosmosUpgrades = $this->polkachuApiClient->getCosmosUpgrades();
        $chains = $request->query->all('chains');
        $events = [];
        foreach ($cosmosUpgrades->getUpgrades() as $upgrade) {
            if ($chains && !\in_array($upgrade->getNetwork(), $chains, true)) {
                continue;
            }

            $timeSpan = new TimeSpan(
                new DateTime($upgrade->getEstimatedUpgradeTime(), true),
                new DateTime($upgrade->getEstimatedUpgradeTime()->modify('+10 minutes'), true)
            );
            $description = 'Network: '.$upgrade->getNetwork().
                '<br>Chain: '.$upgrade->getChainName().
                '<br>Version: '.$upgrade->getNodeVersion().
                '<br>Estimated upgrade time: '.$upgrade->getEstimatedUpgradeTime()->format('Y-m-d H:i:s').
                '<br>Proposal: '.$upgrade->getProposal().
                '<br>Block: '.$upgrade->getBlock().
                '<br>Repo: '.$upgrade->getRepo().'/commit/'.$upgrade->getGitHash().
                '<br>Block link: '.$upgrade->getBlockLink().
                '<br>Guide: '.$upgrade->getGuide().
                '<br>Cosmo Visor Folder: '.$upgrade->getCosmoVisorFolder();
            $event = new Event();
            $event
                ->setSummary('Upgrade '.$upgrade->getChainName().' to '.$upgrade->getNodeVersion())
                ->setOccurrence($timeSpan)
                ->setDescription($description)
            ;
            $events[] = $event;
        }

        if ($request->query->has('view')) {
            return $this->json(\array_map(
                fn (Event $event) => [
                    'title' => $event->getSummary(),
                    'start' => $event->getOccurrence()->getBegin(),
                    'end' => $event->getOccurrence()->getEnd(),
                    'description' => $event->getDescription(),
                ],
                $events
            ));
        }

        $calendar = new Calendar($events);

        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);

        $calendarResponse = new Response((string) $calendarComponent);
        $calendarResponse->headers->set('Content-Type', 'text/calendar; charset=utf-8');
        $calendarResponse->headers->set('Content-Disposition', 'attachment; filename="calendar.ics"');

        return $calendarResponse;
    }
}
