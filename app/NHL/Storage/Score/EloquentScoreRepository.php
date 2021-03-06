<?php

namespace NHL\Storage\Score;

use Illuminate\Config\Repository as ConfigRepository;
use NHL\Storage\Match\MatchRepository;
use Score;

class EloquentScoreRepository implements ScoreRepository {

    /**
     * @var Repository
     */
    private $config;

    /**
     * @var Match
     */
    private $model;
    /**
     * @var MatchRepository
     */
    private $matchRepo;

    /**
     * @param ConfigRepository|Repository $config
     * @param Score $model
     * @param MatchRepository $matchRepo
     */
    public function __construct(ConfigRepository $config, Score $model, MatchRepository $matchRepo)
    {
        $this->config = $config;
        $this->model = $model;
        $this->matchRepo = $matchRepo;
    }

    /**
     * @param $matchId
     * @param $score
     * @return mixed
     */
    public function saveScoreToMatch($matchId, $score)
    {
        $this->guardScore($score);

        $match = $this->matchRepo->byID($matchId);

        return $match->scores()->saveMany($score);
    }

    /**
     * @param $matchId
     * @param $score
     */
    public function updateMatchScore($matchId, $score)
    {
        $this->guardScore($score);

        foreach($score as $s)
        {
            $this->model->where('match_id', '=', $matchId)
                ->update([
                    'home_score' => $s->getHomeScore(),
                    'home_sog'   => $s->getHomeSog(),
                    'away_score' => $s->getAwayScore(),
                    'away_sog'   => $s->getAwaySog(),
                    'shootout' => $s->getShootout(),
                    'overtime' => $s->getOvertime(),
                    'game_status' => $s->getGameStatus(),
                    'game_time' => $s->getGameTime()
                ]);
        }
    }

    /**
     * @param $score
     */
    private function guardScore($score)
    {
        if (is_array($score))
        {
            foreach ($score as $s)
            {
                if (!$s instanceof Score)
                {
                    throw new \InvalidArgumentException('Each item in the score array must be an instance of Score.');
                }
            }
        }
    }

} 