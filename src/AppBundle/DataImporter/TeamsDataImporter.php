<?php

namespace AppBundle\DataImporter;


use GuzzleHttp\Client;

class TeamsDataImporter
{
    /**
     * @return array
     */
    public function getTeams()
    {
        $client = new Client([
            'base_uri' => 'https://erikberg.com/'
        ]);
        $response = $client->request('GET', 'nba/teams.json');
        $teams = json_decode($response->getBody());
        $teamsArray =[];
        foreach ($teams as $team) {
            $team = get_object_vars($team);
            $teamsArray[$team['team_id']] = $team;
        }
        return $teamsArray;
    }
};