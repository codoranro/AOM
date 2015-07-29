<?php

namespace Piwik\Plugins\AOM;

use Exception;
use Piwik\Period\Factory as PeriodFactory;
use Piwik\Archive;
use Piwik\DataTable\Row;
use Piwik\DataTable;
use Piwik\Piwik;
use Piwik\Plugin\Report;
use Piwik\Common;
use Piwik\Db;
use Piwik\Period;
use Piwik\Period\Range;
use Piwik\Segment;
use Piwik\Site;

class API extends \Piwik\Plugin\API
{
    /**
     * Returns all visits with advanced marketing information within the given period.
     *
     * @param int $idSite Id Site
     * @param bool|string $period Period to restrict to when looking at the logs
     * @param bool|string $date Date to restrict to
     * @return array
     * @throws Exception
     */
    public function getVisits($idSite, $period = false, $date = false)
    {
        Piwik::checkUserHasViewAccess($idSite);

        // disabled for multiple dates
        if (Period::isMultiplePeriod($date, $period)) {
            throw new Exception('AOM.getVisits does not support multiple dates.');
        }

        // TODO: This is a period and not a range?!
        // TODO: Timezones and periods might currently not be handled correctly!!

        /** @var Range $period */
        $period = PeriodFactory::makePeriodFromQueryParams(Site::getTimezoneFor($idSite), $period, $date);

        $visits = $this->queryVisits($idSite, $period);

        // Enrich visits with advanced marketing information
        foreach ($visits as &$visit) {
            $this->enrichVisit($visit);
        }

        return $visits;
    }

    /**
     * Returns all visits that happened during the specified period.
     *
     * @param int $idSite Id Site
     * @param Range $period
     * @return array
     * @throws \Exception
     */
    private function queryVisits($idSite, Range $period)
    {
        $sql = 'SELECT
                    idvisit AS visitId,
                    visit_first_action_time AS firstActionTime,
                    CASE referer_type
                        WHEN 1 THEN "direct"
                        WHEN 2 THEN "search_engine"
                        WHEN 3 THEN "website"
                        WHEN 6 THEN "campaign"
                        ELSE ""
                    END AS source,
                    referer_name AS refererName,
                    referer_keyword AS refererKeyword,
                    referer_url AS refererUrl,
                    aom_ad_id AS adId
                FROM ' . Common::prefixTable('log_visit') . ' AS log_visit
                WHERE
                    idsite = ? AND
                    visit_first_action_time >= ? AND
                    visit_first_action_time <= ?
                ORDER BY visit_last_action_time ASC';

        $visits = Db::fetchAll(
            $sql,
            [
                $idSite,
                $period->getDateStart()->toString('Y-m-d 00:00:00'),
                $period->getDateEnd()->toString('Y-m-d 23:59:59'),
            ]
        );

        return $visits;
    }


    /**
     * Enriches a specific visit with advanced marketing information when applicable.
     *
     * @param array &$visit
     * @return array
     * @throws Exception
     */
    private function enrichVisit(&$visit)
    {
        if (0 === strlen($visit['adId']) || !strpos($visit['adId'], '|')) {
            return $visit;
        }

        $ad = explode('|', $visit['adId']);

        // AdWords
        if ($ad[0] === 'adwords') {

            // TODO: Check if AdWords is active

            // TODO: Store 'm' instead of 'Mobile devices with full browsers' etc.?!
            // TODO: Store 'd' instead of 'Display Network' etc.?!

            $sql = 'SELECT
                        campaign_id AS campaignId,
                        campaign,
                        ad_group_id AS adGroupId,
                        ad_group AS adGroup,
                        keyword_id AS keywordId,
                        keyword_placement AS keywordPlacement,
                        criteria_type AS criteriaType,
                        network,
                        device,
                        (cost / clicks) AS cpc
                    FROM ' . Common::prefixTable('aom_adwords') . '
                    WHERE
                        date = ? AND
                        campaign_id = ? AND
                        ad_group_id = ? AND
                        keyword_id = ? AND
                        network = ? AND
                        device = ?';

            $visit['ad'] = Db::fetchRow(
                $sql,
                [
                    date('Y-m-d', strtotime($visit['firstActionTime'])),
                    $ad[1],                                     // {campaignid}
                    $ad[2],                                     // {adgroupid}
                    substr($ad[4], strrpos($ad[4], '-' ) + 1),  // {targetid}, e.g. kwd-385125304
                    ('g' == $ad[6] ? 'Search Network' : 'Display Network'),
                    ('m' == $ad[7]
                        ? 'Mobile devices with full browsers'
                        : ('c' == $ad[7] ? 'Computers' : 'Tablets with full browsers')),
                ]
            );

            return $visit;
        }

        // Criteo
        if ($ad[0] === 'criteo') {

            // TODO: Check if Criteo is active

            $sql = 'SELECT
                        campaign_id AS campaignId,
                        campaign,
                        (cost / clicks) AS cpc
                    FROM ' . Common::prefixTable('aom_criteo') . '
                    WHERE
                        date = ? AND
                        campaign_id = ?';

            $visit['ad'] = Db::fetchRow(
                $sql,
                [
                    date('Y-m-d', strtotime($visit['firstActionTime'])),
                    $ad[1],
                ]
            );

            return $visit;
        }

        return $visit;
    }
}