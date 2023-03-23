<?php

declare(strict_types=1);

namespace App\Controller\Cosmos;

use App\Controller\BaseController;
use App\Form\Cosmos\SignerFormHandler;
use App\Service\Polkachu\PolkachuApiClient;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\Entity\TimeZone;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpgradesController extends BaseController
{
    public function __construct(
        private readonly PolkachuApiClient $polkachuApiClient
    )
    {
    }

    /**
     * @Route("/cosmos/upgrades", name="app_cosmos_upgrades_index")
     */
    public function indexAction(Request $request, SignerFormHandler $signerFormHandler): Response
    {
        $formResponse = $this->createAndHandleFormHandler($signerFormHandler, $request);

        return $this->render('cosmos/upgrades/index.html.twig', [
            'form' => $formResponse->getForm()->createView(),
        ]);
    }

    /**
     * @Route("/cosmos/upgrades/ical.ics", name="app_cosmos_upgrades_ical")
     */
    public function icalAction(Request $request): Response
    {
        $cosmosUpgrades = $this->polkachuApiClient->getCosmosUpgrades();
        $chains = $request->query->all('chains');
        $events = [];
        foreach ($cosmosUpgrades->getUpgrades() as $upgrade) {
            if ($chains && !\in_array($upgrade->getNetwork(), $chains)) {
                continue;
            }

            $timeSpan = new TimeSpan(
                new DateTime($upgrade->getEstimatedUpgradeTime(), false),
                new DateTime($upgrade->getEstimatedUpgradeTime()->modify('+10 minutes'), false)
            );
            $description = 'Network: ' . $upgrade->getNetwork() .
                '<br>Chain: ' . $upgrade->getChainName() .
                '<br>Version: ' . $upgrade->getNodeVersion() .
                '<br>Estimated upgrade time: ' . $upgrade->getEstimatedUpgradeTime()->format('Y-m-d H:i:s') .
                '<br>Proposal: ' . $upgrade->getProposal() .
                '<br>Block: ' . $upgrade->getBlock() .
                '<br>Repo: ' . $upgrade->getRepo().'/commit/'.$upgrade->getGitHash() .
                '<br>Block link: ' . $upgrade->getBlockLink() .
                '<br>Guide: ' . $upgrade->getGuide() .
                '<br>Cosmo Visor Folder: ' . $upgrade->getCosmoVisorFolder();
            $event = new Event();
            $event
                ->setSummary('Upgrade ' . $upgrade->getChainName() . ' to ' . $upgrade->getNodeVersion())
                ->setOccurrence($timeSpan)
                ->setDescription($description)
            ;
            $events[] = $event;
        }

        $calendar = new Calendar($events);
        $calendar->addTimeZone(TimeZone::createFromPhpDateTimeZone(new \DateTimeZone('UTC')));

        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);

        $calendarResponse = new Response((string) $calendarComponent);
        $calendarResponse->headers->set('Content-Type', 'text/calendar; charset=utf-8');
        $calendarResponse->headers->set('Content-Disposition', 'attachment; filename="calendar.ics"');

        return $calendarResponse;
    }

}