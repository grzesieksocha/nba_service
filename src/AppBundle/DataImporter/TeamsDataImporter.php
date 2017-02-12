<?php declare(strict_types = 1);

namespace AppBundle\DataImporter;

use AppBundle\Exceptions\RateLimitException;
use GuzzleHttp\Client;

/**
 * Class TeamsDataImporter
 * @package AppBundle\DataImporter
 */
class TeamsDataImporter
{
    /**
     * @return string[]
     */
    public function getTeams()
    {
        $client = new Client([
            'base_uri' => 'https://erikberg.com/'
        ]);
        $response = $client->request('GET', 'nba/teams.json');
        if (200 === $response->getStatusCode()) {
            $teams = json_decode($response->getBody());
            $teamsArray = [];
            foreach ($teams as $team) {
                $team = get_object_vars($team);
                $teamsArray[$team['team_id']] = $team;
            }
            return $teamsArray;
        } else {
            throw new RateLimitException('Too many requests!');
        }
    }
};