<?php

namespace NHL\Schedule\HTML;

use NHL\Schedule\ScheduleImporter as ScheduleImporterInterface;
use NHL\Common\FileDownloader;
use NHL\Exceptions\NonExistentTeamException;
use PHPHtmlParser\Dom;
use Sunra\PhpSimple\HtmlDomParser;
use Illuminate\Config\Repository as ConfigRepository;

class ScheduleImporter implements ScheduleImporterInterface {

    /**
     * @var Dom
     */
    private $dom;

    /**
     * @var ScheduleDownloader
     */
    private $fileDownloader;

    /**
     * @var ConfigRepository
     */
    private $config;

    /**
     * @param Dom                $dom
     * @param FileDownloader $fileDownloader
     * @param ConfigRepository   $config
     */
    public function __construct(Dom $dom, FileDownloader $fileDownloader, ConfigRepository $config)
    {
        $this->dom = $dom;
        $this->fileDownloader = $fileDownloader;
        $this->config = $config;
    }

    /**
     * Import the entire season schedule for a particular team.
     *
     * @param $teamId
     * @throws NonExistentTeamException
     * @return array
     */
    public function bySeason($teamId)
    {
        // Make sure the team exists.
        if ( ! $this->config->has("nhl.teams.{$teamId}"))
        {
            throw new NonExistentTeamException;
        }

        // Download the schedule and return it as a string.
        $htmlString = $this->fileDownloader->get(sprintf($this->config->get('nhl.htmlTeamSeasonScheduleUrl'), $teamId));

        return $this->parse($htmlString);
    }

    /**
     * Parse the html. Each table row will them become an
     *
     * @param $htmlString
     * @return array
     */
    private function parse($htmlString)
    {
        // Parse the html.
        $dom = HtmlDomParser::str_get_html($htmlString);

        // Find the schedule table.
        $matchesTable = $dom->find('.schedTbl tbody tr');

        // Remove the table header.
        array_shift($matchesTable);

        return $matchesTable;
    }
}