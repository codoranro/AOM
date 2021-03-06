<?php
/**
 * AOM - Piwik Advanced Online Marketing Plugin
 *
 * @author Daniel Stonies <daniel.stonies@googlemail.com>
 */
namespace Piwik\Plugins\AOM\tests\Fixtures;

use Piwik\Plugins\AOM\SystemSettings;
use Piwik\Tests\Framework\Fixture;
use Piwik;
use Piwik\Date;

class BasicFixtures extends Fixture
{
    public $tokenAuth;

    public function setUp()
    {
        $this->setUpWebsite();

        // since we're changing the list of activated plugins, we have to make sure file caches are reset
        Piwik\Cache::flushAll();

        $user = self::createSuperUser();
        $this->tokenAuth = $user['token_auth'];

        $settings = new SystemSettings();
        $settings->paramPrefix->setValue('aom');
        $settings->createNewVisitWhenCampaignChanges->setValue(true);
        $settings->platformAdWordsIsActive->setValue(true);

        // TODO: Add tests for tracking variant "gclid"!
        $settings->platformAdWordsTrackingVariant
            ->setValue(Piwik\Plugins\AOM\Platforms\AdWords\AdWords::TRACKING_VARIANT_REGULAR_PARAMS);
        $settings->platformBingIsActive->setValue(true);
        $settings->platformCriteoIsActive->setValue(true);
        $settings->platformFacebookAdsIsActive->setValue(true);
        $settings->save();
    }

    public function tearDown()
    {
        // empty
    }

    protected function setUpWebsite()
    {
        $idSite = self::createWebsite('2015-12-01 01:23:45', $ecommerce = 1);
        $this->assertTrue($idSite === 1);
    }

    public function provideContainerConfig()
    {
        return [
            'observers.global' => \DI\add([
                ['Environment.bootstrapped', function () {
                    $plugins = Piwik\Config::getInstance()->Plugins['Plugins'];
                    $plugins[] = 'AOM';
                    Piwik\Config::getInstance()->Plugins['Plugins'] = $plugins;
                }],
            ]),
        ];
    }

    /**
     * @param \PiwikTracker $t
     * @param $hourForward
     * @param $dateTime
     * @throws \Exception
     */
    public function moveTimeForward(\PiwikTracker $t, $hourForward, $dateTime)
    {
        $t->setForceVisitDateTime(Date::factory($dateTime)->addHour($hourForward)->getDatetime());
    }
}
