<?php


namespace AppBundle\DataImporter;


use AppBundle\Entity\Conference;
use AppBundle\Entity\Division;
use AppBundle\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

class TeamsSaver
{
    /** @var  EntityManager */
    private $doctrineRegistry;

    public function __construct(Registry $doctrineRegistry)
    {
        $this->doctrineRegistry = $doctrineRegistry;
    }

    public function saveTeams($teams)
    {
        $em = $this->doctrineRegistry->getManager();
        $teamRepository = $this->doctrineRegistry->getRepository('AppBundle:Team');
        $conferenceRepository = $this->doctrineRegistry->getRepository('AppBundle:Conference');
        $divisionRepository = $this->doctrineRegistry->getRepository('AppBundle:Division');

        foreach ($teams as $team) {
            if (null === $divisionRepository->findOneBy(['name' => $team['division']])) {
                $newDivision = new Division();
                $newDivision->setName($team['division']);
                $newDivision->setItemStatus(Division::V_ITEM_STATUS_ACTIVE);
                $em->persist($newDivision);
            }
            if (null === $conferenceRepository->findOneBy(['name' => $team['conference']])) {
                $newConference = new Conference();
                $newConference->setName($team['conference']);
                $newConference->setItemStatus(Conference::V_ITEM_STATUS_ACTIVE);
                $em->persist($newConference);
            }
            $em->flush();

            if (null === $teamRepository->findOneBy(['short' => $team['abbreviation']])) {
                $newTeam = new Team();
                $newTeam->setShort($team['abbreviation']);
                $newTeam->setFirstName($team['first_name']);
                $newTeam->setLastName($team['last_name']);
                $newTeam->setCity($team['city']);
                $newTeam->setState($team['state']);
                $newTeam->setSiteName($team['site_name']);
                $newTeam->setItemStatus(Team::V_ITEM_STATUS_ACTIVE);

                $division = $divisionRepository->findOneBy(['name' => $team['division']]);
                $conference = $conferenceRepository->findOneBy(['name' => $team['conference']]);

                if ($division) {
                    $newTeam->setDivision($division);
                }
                if ($conference) {
                    $newTeam->setConference($conference);
                }

                $em->persist($newTeam);
            }
        }
        $em->flush();
    }
}