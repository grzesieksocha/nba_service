<?php


namespace AppBundle\DataImporter;


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
        $repository = $this->doctrineRegistry->getRepository('AppBundle:Team');
        foreach ($teams as $team) {
            if (empty($repository->findBy(['short' => $team['abbreviation']]))) {
                $newTeam = new Team();
                $newTeam->setShort($team['abbreviation']);
                $newTeam->setName($team['full_name']);

                $em->persist($newTeam);
            }
        }
        $em->flush();
    }
}