<?php
/**
 * AOM - Piwik Advanced Online Marketing Plugin
 *
 * @author Daniel Stonies <daniel.stonies@googlemail.com>
 */
namespace Piwik\Plugins\AOM\Platforms\AdWords;

use Piwik\Common;
use Piwik\Db;
use Piwik\Metrics\Formatter;
use Piwik\Piwik;
use Piwik\Plugins\AOM\AOM;
use Piwik\Plugins\AOM\Platforms\Platform;
use Piwik\Plugins\AOM\Platforms\PlatformInterface;

class AdWords extends Platform implements PlatformInterface
{
    /**
     * AdWords can either track and merge via gclid (auto-tagging) or via regular-params but not both at the same time!
     */
    const TRACKING_VARIANT_GCLID = 'gclid';
    const TRACKING_VARIANT_REGULAR_PARAMS = 'regular-params';

    /**
     * @var array All supported tracking variants
     */
    public static $trackingVariants = [
        self::TRACKING_VARIANT_GCLID,
        self::TRACKING_VARIANT_REGULAR_PARAMS,
    ];

    const CRITERIA_TYPE_AGE = 'age';
    const CRITERIA_TYPE_GENDER = 'gender';
    const CRITERIA_TYPE_KEYWORD = 'keyword';
    const CRITERIA_TYPE_PLACEMENT = 'placement';
    const CRITERIA_TYPE_USER_INTEREST = 'user interest';
    const CRITERIA_TYPE_USER_LIST = 'user list';

    /**
     * @var array All supported criteria types
     */
    public static $criteriaTypes = [
        self::CRITERIA_TYPE_AGE,
        self::CRITERIA_TYPE_GENDER,
        self::CRITERIA_TYPE_KEYWORD,
        self::CRITERIA_TYPE_PLACEMENT,
        self::CRITERIA_TYPE_USER_LIST,
        self::CRITERIA_TYPE_USER_INTEREST,
    ];

    /**
     * @see https://developers.google.com/adwords/api/docs/appendix/reports/all-reports#adnetworktype2
     */
    const NETWORK_CONTENT = 'Display Network';
    const NETWORK_SEARCH = 'Google search';
    const NETWORK_SEARCH_PARTNERS = 'Search partners';
    const NETWORK_YOUTUBE_SEARCH = 'YouTube Search';
    const NETWORK_YOUTUBE_WATCH = 'YouTube Videos';
    const NETWORK_UNKNOWN = 'unknown';

    /**
     * @var array All supported networks
     */
    public static $networks = [
        self::NETWORK_CONTENT => 'd',
        self::NETWORK_SEARCH => 'g',
        self::NETWORK_SEARCH_PARTNERS => 's',
        self::NETWORK_YOUTUBE_SEARCH => null,
        self::NETWORK_YOUTUBE_WATCH => null,
        self::NETWORK_UNKNOWN => null,
    ];

    /**
     * @see https://developers.google.com/adwords/api/docs/appendix/reports/click-performance-report#device
     */
    const DEVICE_COMPUTERS = 'Computers';
    const DEVICE_MOBILE = 'Mobile devices with full browsers';
    const DEVICE_TABLETS = 'Tablets with full browsers';

    /**
     * @var array All supported devices
     */
    public static $devices = [
        self::DEVICE_COMPUTERS => 'c',
        self::DEVICE_MOBILE => 'm',
        self::DEVICE_TABLETS => 't',
    ];

    /**
     * Whether or not this platform has been activated in the plugin's configuration.
     *
     * @return string
     */
    public function getTrackingVariant()
    {
        $trackingVariant = $this->getSettings()->{'platformAdWordsTrackingVariant'}->getValue();

        return in_array($trackingVariant, self::$trackingVariants) ? $trackingVariant : self::TRACKING_VARIANT_GCLID;
    }

    /**
     * Extracts advertisement platform specific data from the query params based on the configured tracking variant
     * (either gclid or regular parameters) and validates it:
     *  - When "gclid" is configured, then all other params are being ignored
     *  - When "regular-params" are configured, then gclid is ignored but a lot of ValueTrack params are required
     *
     * @param string $paramPrefix
     * @param array $queryParams
     * @return mixed
     */
    public function getAdParamsFromQueryParams($paramPrefix, array $queryParams)
    {
        if (self::TRACKING_VARIANT_GCLID === $this->getTrackingVariant()) {

            // No validation possible, as there either is a gclid or not (the _platform param won't be set!)
            return array_key_exists('gclid', $queryParams)
                ? [
                    'platform' => AOM::PLATFORM_AD_WORDS,
                    'gclid' => $queryParams['gclid'],
                ] : null;

        } elseif (self::TRACKING_VARIANT_REGULAR_PARAMS === $this->getTrackingVariant()) {

            // Validate required params
            $missingParams = array_diff(
                [
                    $paramPrefix . '_campaign_id',
                    $paramPrefix . '_ad_group_id',
                    $paramPrefix . '_feed_item_id',
                    $paramPrefix . '_target_id',
                    $paramPrefix . '_creative',
                    $paramPrefix . '_placement',
                    $paramPrefix . '_target',
                    $paramPrefix . '_network',
                ],
                array_keys($queryParams)
            );
            if (count($missingParams)) {
                $this->getLogger()->warning(
                    'Visit with platform ' . AOM::PLATFORM_AD_WORDS
                    . ' without required param' . (count($missingParams) > 0 ? 's' : '') . ': '
                    . implode(', ', $missingParams)
                );

                return null;
            }
        }

        $adParams = [
            'platform' => AOM::PLATFORM_AD_WORDS,
        ];

        // Add params
        if (array_key_exists($paramPrefix . '_campaign_id', $queryParams)) {
            $adParams['campaignId'] = $queryParams[$paramPrefix . '_campaign_id'];
        }
        if (array_key_exists($paramPrefix . '_ad_group_id', $queryParams)) {
            $adParams['adGroupId'] = $queryParams[$paramPrefix . '_ad_group_id'];
        }
        if (array_key_exists($paramPrefix . '_feed_item_id', $queryParams)) {
            $adParams['feedItemId'] = $queryParams[$paramPrefix . '_feed_item_id'];
        }
        if (array_key_exists($paramPrefix . '_target_id', $queryParams)) {
            $adParams['targetId'] = $queryParams[$paramPrefix . '_target_id'];
        }
        if (array_key_exists($paramPrefix . '_creative', $queryParams)) {
            $adParams['creative'] = $queryParams[$paramPrefix . '_creative'];
        }
        if (array_key_exists($paramPrefix . '_placement', $queryParams)) {
            $adParams['placement'] = $queryParams[$paramPrefix . '_placement'];
        }
        if (array_key_exists($paramPrefix . '_target', $queryParams)) {
            $adParams['target'] = $queryParams[$paramPrefix . '_target'];
        }
        if (array_key_exists($paramPrefix . '_network', $queryParams)) {
            $adParams['network'] = $queryParams[$paramPrefix . '_network'];
        }
        if (array_key_exists($paramPrefix . '_ad_position', $queryParams)) {
            $adParams['adPosition'] = $queryParams[$paramPrefix . '_ad_position'];
        }
        if (array_key_exists($paramPrefix . '_loc_physical', $queryParams)) {
            $adParams['locPhysical'] = $queryParams[$paramPrefix . '_loc_physical'];
        }
        if (array_key_exists($paramPrefix . '_loc_Interest', $queryParams)) {
            $adParams['locInterest'] = $queryParams[$paramPrefix . '_loc_Interest'];
        }

        return $adParams;
    }

    /**
     * @inheritdoc
     */
    public function getAdDataFromAdParams($idsite, array $adParams, $date = null)
    {
        if (!$date) {
            $date = date('Y-m-d', strtotime(AOM::convertUTCToLocalDateTime(date('Y-m-d H:i:s'), $idsite)));
        }

        $adData = $this->getAdData(
            $idsite,
            $date,
            $adParams
        );

        if (!$adData[0] && array_key_exists('campaignId', $adParams) && array_key_exists('adGroupId', $adParams)) {
            $adData = [null, $this::getHistoricalAdData($idsite, $adParams['campaignId'], $adParams['adGroupId'])];
        }

        return $adData;
    }

    /**
     * Tries to match ad params with imported platform data for a specific date.
     *
     * @param int $idsite
     * @param string $date
     * @param array $adParams
     * @return array|null Either a [{visitId}, {adData}] array or null (when no adData could be found)
     * @throws \Exception
     */
    public static function getAdData($idsite, $date, $adParams)
    {
        // We cannot get adData for adParams when required data is missing
        if (!array_key_exists('network', $adParams) || 0 === strlen($adParams['network'])
            || !array_key_exists('campaignId', $adParams) || 0 === strlen($adParams['campaignId'])
            || !array_key_exists('adGroupId', $adParams) || 0 === strlen($adParams['adGroupId'])
            || ((!array_key_exists('placement', $adParams) || 0 === strlen($adParams['placement']))
                && (!array_key_exists('targetId', $adParams) || 0 === strlen($adParams['targetId'])))
        ) {
            return null;
        }

        if ($adParams['network'] == 'd') {

            $query = 'SELECT * FROM ' . AOM::getPlatformDataTableNameByPlatformName(AOM::PLATFORM_AD_WORDS) . '
                WHERE idsite = ' . $idsite . ' AND date = "' . $date . '" AND network = "d"
                AND campaign_id = "' . $adParams['campaignId'] . '" AND ad_group_id = "' . $adParams['adGroupId'] . '"
                AND keyword_placement = "' . $adParams['placement'] . '"';

        } else {

            $targetId = $adParams['targetId'];
            if (strpos($adParams['targetId'], 'kwd-') !== false) {
                $targetId = substr($adParams['targetId'], strpos($adParams['targetId'], 'kwd-') + 4);
            }

            $query = 'SELECT * FROM ' . AOM::getPlatformDataTableNameByPlatformName(AOM::PLATFORM_AD_WORDS) . '
                WHERE idsite = ' . $idsite . ' AND date = "' . $date . '" AND network = "' . $adParams['network'] . '"
                AND campaign_id = "' . $adParams['campaignId'] . '" AND ad_group_id = "' . $adParams['adGroupId'] . '"
                AND keyword_id = "' . $targetId . '"';
        }

        $result = Db::fetchAll($query);

        if (count($result) > 1) {
            throw new \Exception('Found more than one match for exact match query: ' . $query);
        } elseif(count($result) == 0) {
            return null;
        }

        return [$result[0]['id'], $result[0]];
    }


    /**
     * Tries to identify campaign name, ad group name etc. from historical imported data by given ad param ids.
     *
     * @param int $idsite
     * @param int $campaignId
     * @param int $adGroupId
     * @return array|null
     * @throws \Exception
     */
    public static function getHistoricalAdData($idsite, $campaignId, $adGroupId)
    {
        // We cannot get the desired data as required attributes are missing (we might get this data later via gclid!)
        if (0 === strlen($campaignId) || 0 === strlen($adGroupId)) {
            return null;
        }

        $result = Db::fetchRow(
            'SELECT * FROM ' . AOM::getPlatformDataTableNameByPlatformName(AOM::PLATFORM_AD_WORDS) . '
                WHERE idsite = ? AND campaign_id = ? AND ad_group_id = ? ORDER BY date DESC LIMIT 1',
            [
                $idsite,
                $campaignId,
                $adGroupId
            ]
        );

        if ($result) {

            // Keep generic date-independent information only
            return [
                'campaign_id' => $campaignId,
                'campaign' => $result['campaign'],
                'ad_group_id' => $adGroupId,
                'ad_group' => $result['ad_group'],
            ];
        }

        return null;
    }

    /**
     * Activates sub tables for the marketing performance report in the Piwik UI for AdWords.
     *
     * @return MarketingPerformanceSubTables
     */
    public function getMarketingPerformanceSubTables()
    {
        return new MarketingPerformanceSubTables();
    }

    /**
     * Returns a platform-specific description of a specific visit optimized for being read by humans or false when no
     * platform-specific description is available.
     *
     * @param int $idVisit
     * @return string|false
     */
    public static function getHumanReadableDescriptionForVisit($idVisit)
    {
        $visit = Db::fetchRow(
            'SELECT
                idsite,
                platform_data,
                cost
             FROM ' . Common::prefixTable('aom_visits') . '
             WHERE piwik_idvisit = ?',
            [
                $idVisit,
            ]
        );

        if ($visit) {

            $formatter = new Formatter();

            $platformData = json_decode($visit['platform_data'], true);

            return Piwik::translate(
                'AOM_Platform_VisitDescription_AdWords',
                [
                    $formatter->getPrettyMoney($visit['cost'], $visit['idsite']),
                    $platformData['account'],
                    $platformData['campaign'],
                    $platformData['ad_group'],
                    $platformData['keyword_placement'],
                ]
            );
        }

        return false;
    }
}
