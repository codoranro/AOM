<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit33de86d9f0090fc77c519677ca4060d4
{
    public static $files = array (
        '7e702cccdb9dd904f2ccf22e5f37abae' => __DIR__ . '/..' . '/facebook/php-sdk-v4/src/Facebook/polyfills.php',
    );

    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Facebook\\' => 9,
            'FacebookAds\\' => 12,
        ),
        'B' => 
        array (
            'Bramus\\Monolog\\' => 15,
            'Bramus\\Ansi\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Facebook\\' => 
        array (
            0 => __DIR__ . '/..' . '/facebook/php-sdk-v4/src/Facebook',
        ),
        'FacebookAds\\' => 
        array (
            0 => __DIR__ . '/..' . '/facebook/php-ads-sdk/src/FacebookAds',
        ),
        'Bramus\\Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/bramus/monolog-colored-line-formatter/src',
        ),
        'Bramus\\Ansi\\' => 
        array (
            0 => __DIR__ . '/..' . '/bramus/ansi-php/src',
        ),
    );

    public static $classMap = array (
        'AdWordsConstants' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Lib/AdWordsConstants.php',
        'AdWordsSoapClient' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Lib/AdWordsSoapClient.php',
        'AdWordsSoapClientFactory' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Lib/AdWordsSoapClientFactory.php',
        'AdWordsUser' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Lib/AdWordsUser.php',
        'ApiError' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'BatchJobOpsMutate' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/BatchJobClasses.php',
        'BatchJobOpsMutateResponse' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/BatchJobClasses.php',
        'BatchJobUtils' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/BatchJobUtils.php',
        'BatchJobUtilsDelegate' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/BatchJobUtilsDelegate.php',
        'DateRange' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'DfpSoapClient' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/Dfp/Lib/DfpSoapClient.php',
        'DfpSoapClientFactory' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/Dfp/Lib/DfpSoapClientFactory.php',
        'DfpUser' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/Dfp/Lib/DfpUser.php',
        'DownloadFormat' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'ErrorList' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/BatchJobClasses.php',
        'MutateResult' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/BatchJobClasses.php',
        'Operand' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/BatchJobClasses.php',
        'OrderBy' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'Paging' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'Predicate' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'PredicateOperator' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'ReportDefinition' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'ReportDefinitionDateRangeType' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'ReportDefinitionReportType' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'ReportDownloadError' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'ReportUtils' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportUtils.php',
        'ReportUtilsDelegate' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportUtilsDelegate.php',
        'Selector' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
        'SortOrder' => __DIR__ . '/..' . '/googleads/googleads-php-lib/src/Google/Api/Ads/AdWords/Util/v201609/ReportClasses.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit33de86d9f0090fc77c519677ca4060d4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit33de86d9f0090fc77c519677ca4060d4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit33de86d9f0090fc77c519677ca4060d4::$classMap;

        }, null, ClassLoader::class);
    }
}