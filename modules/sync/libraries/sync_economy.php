<?php

/**
 * sync
 *
 * Esta librería se encarga de todo el proceso de sincronización con hattrick para obtener datos.
 *
 * @package
 *
 * @author ImHosb
 * @author www.Imhosb.com
 *
 * @access public
 */
class Sync_economy extends Sync_sync {

    /**
     * sync::__construct()
     *
     * @return sync
     */
    function __construct($user_id) {
        parent::__construct($user_id);
    }

    function get_weekly_economy() {
        $userProfile = $this->sync_model->get_user_profile();

        $league = $this->HT->getWorldDetailsByLeagueId($userProfile['LeagueID'])->xmlText;
        $xml = simplexml_load_string($league);
        $season = (int) $xml->LeagueList->League->Season;
        $matchRound = (int) $xml->LeagueList->League->MatchRound;
        $economyDate = (string) $xml->LeagueList->League->EconomyDate;
        $seriesMatchDate = (string) $xml->LeagueList->League->SeriesMatchDate;
        $lastEconomyDate = $userProfile['EconomyDate'];
        $lastSeriesMatchDate = $userProfile['SeriesMatchDate'];

        //if($economyDate != $lastEconomyDate) {
        if($seriesMatchDate >= $lastSeriesMatchDate) {
            --$matchRound;
            if($matchRound <= 0) {
                $matchRound += 16;
                --$season;
            }
        }
            // Economy data
            $economy = $this->HT->getEconomy(HTMoney::Sverige)->xmlText;
            $xml = simplexml_load_string($economy);
            $userProfile = $this->sync_model->get_user_profile();
            $currencyRate = $userProfile['CurrencyRate'];
            $economy = (array) $xml->Team;

            $data['team_id'] = $economy['team_id'];
            $data['Season'] = $season;
            $data['MatchRound'] = $matchRound;

            $data['IncomeSpectators'] = (float) round(str_replace(',', '.', $economy['LastIncomeSpectators']) / $currencyRate);
            $data['IncomeSponsors'] = (float) round(str_replace(',', '.', $economy['LastIncomeSponsors']) / $currencyRate);
            $data['IncomeSoldPlayers'] = (float) round(str_replace(',', '.', $economy['LastIncomeSoldPlayers']) / $currencyRate);
            $data['IncomeSoldPlayersCommission'] = (float) round(str_replace(',', '.', $economy['LastIncomeSoldPlayersCommission']) / $currencyRate);
            $data['IncomeTemporary'] = (float) round(str_replace(',', '.', $economy['LastIncomeTemporary']) / $currencyRate);

            $data['CostsArena'] = (float) round(str_replace(',', '.', $economy['LastCostsArena']) / $currencyRate);
            $data['CostsPlayers'] = (float) round(str_replace(',', '.', $economy['LastCostsPlayers']) / $currencyRate);
            $data['CostsFinancial'] = (float) round(str_replace(',', '.', $economy['LastCostsFinancial']) / $currencyRate);
            $data['CostsStaff'] = (float) round(str_replace(',', '.', $economy['LastCostsStaff']) / $currencyRate);
            $data['CostsBoughtPlayers'] = (float) round(str_replace(',', '.', $economy['LastCostsBoughtPlayers']) / $currencyRate);
            $data['CostsArenaBuilding'] = (float) round(str_replace(',', '.', $economy['LastCostsArenaBuilding']) / $currencyRate);
            $data['CostsTemporary'] = (float) round(str_replace(',', '.', $economy['LastCostsTemporary']) / $currencyRate);
            $data['CostsYouth'] = (float) round(str_replace(',', '.', $economy['LastCostsYouth']) / $currencyRate);

            $data['WeeksTotal'] = (float) round(str_replace(',', '.', $economy['LastWeeksTotal']) / $currencyRate);

        if(!$this->sync_model->isExists('mg_economy_weekly', 'team_id', $this->teamID, 'Season', $season, 'MatchRound', $matchRound))
            $this->sync_model->insertXML('mg_economy_weekly', $data);
        //}
    }

    function build_seasonal_economy () {
        $seasons = $this->economy_model->getSeasonsFromWeeklyEconomy($this->team_id);

        if(isset($seasons))
        foreach ($seasons as $season) {
            $currentSeason = $this->training_model->getCurrentSeason($this->team_id);

            if($season == $currentSeason) {

                $data = $this->buildSeasonalData($season);
                $filterKeys[] = 'team_id';
                $filterValues[] = $this->teamID;
                $filterKeys[] = 'Season';
                $filterValues[] = $season;

                $this->sync_model->updateXML('mg_economy_weekly', $data, array('team_id' => $this->teamID, 'Season' => $season));

            } elseif(!$this->sync_model->isExists('mg_economy_seasonal', 'team_id', $this->teamID, 'Season', $season)) {
                $data = $this->buildSeasonalData($season);
                $this->sync_model->insertXML('mg_economy_seasonal', $data);
            }
        }
    }

    private function buildSeasonalData ($season) {
        $weeklyEconomy = $this->economy_model->getWeeklyEconomy($this->teamID, $season);
        $fields = $this->sync_model->getFields('mg_economy_weekly');

        $data = array();
        $data['team_id'] = $this->teamID;
        $data['Season'] = $season;
        while($result = mysql_fetch_assoc($weeklyEconomy)) {
            foreach ($fields as $field) {
                if($field != 'ID' AND $field != 'team_id' AND $field != 'Season' AND $field != 'MatchRound')
                    $data[$field] += $result[$field];
            }
        }
        return $data;
    }

    function build_economy_market_price($playerID) {
        $currentSeason = $this->economy_model->get_user_profile('Season');
        if (!$this->sync_model->isExists('mg_economy_marketprice', 'team_id', $this->teamID, 'PlayerID', $playerID, 'Season', $currentSeason)) {
            $this->economy_model->buildEconomyMarketPrice($playerID, $this->teamID, $currentSeason);
        }
    }

}

?>
