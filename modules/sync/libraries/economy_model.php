<?php

class economy_model extends sync_model {

    function __construct($route, $user_id) {
        $this->route = $route;
        $this->userID = $user_id;
        parent::__construct($this->route, $this->userID);
    }

    function getSeasonsFromWeeklyEconomy($team_id) {

        $query = $this->myQuery("SELECT DISTINCT season FROM mg_economy_weekly WHERE team_id='$team_id'");
        while($result = mysql_fetch_assoc($query)) {
            $data[] = $result;
        }

        if(isset($data))
            foreach ($data as $value)
                $array[] = $value['season'];

        return $array;
    }

    function getWeeklyEconomy($team_id, $season) {
        $query = $this->myQuery("SELECT * FROM mg_economy_weekly WHERE team_id='$team_id' AND Season='$season'");

        return $query;
    }

    function buildEconomyMarketPrice ($playerID, $team_id, $season) {
        for ($matchRound = 1; $matchRound <= 16; ++$matchRound) {
            $data = array(
                'PlayerID' => $playerID,
                'team_id' => $team_id,
                'Season' => $season,
                'MatchRound' => $matchRound,
                'Price' => 0
            );
            $values = $this->buildInsertQuery($data);
            $this->myQuery("INSERT INTO mg_economy_marketprice VALUES ($values)", $this->debug);
        }
    }
}

?>