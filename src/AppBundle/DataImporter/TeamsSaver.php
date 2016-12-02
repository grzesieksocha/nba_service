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
            if (empty($divisionRepository->findBy(['name' => $team['division']]))) {
                $newDivision = new Division();
                $newDivision->setName($team['division']);
                $em->persist($newDivision);
            }
            if (empty($conferenceRepository->findBy(['name' => $team['conference']]))) {
                $newConference = new Conference();
                $newConference->setName($team['conference']);
                $em->persist($newConference);
            }
            $em->flush();

            if (empty($teamRepository->findBy(['short' => $team['abbreviation']]))) {
                $newTeam = new Team();
                $newTeam->setShort($team['abbreviation']);
                $newTeam->setFirstName($team['first_name']);
                $newTeam->setLastName($team['last_name']);
                $newTeam->setCity($team['city']);
                $newTeam->setState($team['state']);
                $newTeam->setSiteName($team['site_name']);

                $division = $divisionRepository->findBy(['name' => $team['division']]);
                $conference = $conferenceRepository->findBy(['name' => $team['conference']]);

                if (!empty($division)) {
                    $newTeam->setDivision($division[0]);
                }
                if (!empty($conference)) {
                    $newTeam->setConference($conference[0]);
                }

                $em->persist($newTeam);
            }
        }
        $em->flush();
    }
}