<?php

    /*
    |--------------------------------------------------------------------------
    | Default Configurations
    |--------------------------------------------------------------------------
    |
    | This file holds the default configurations for WebCeption. Rather than editing
    | this file copy `codeception-local-sample.php` to `codeception-local.php` and
    | update that file with your custom configs.
    |
    */

$localConfig = array();
if (file_exists(__DIR__.'/codeception-local.php')) {
    $localConfig = require(__DIR__.'/codeception-local.php');
}

// eBRÁNA - load modules
$applicationDir = realpath(__DIR__.
    DIRECTORY_SEPARATOR.'..'.
    DIRECTORY_SEPARATOR.'..'.
    DIRECTORY_SEPARATOR.'..'.
    DIRECTORY_SEPARATOR.'..'.
    DIRECTORY_SEPARATOR.'application'
);

$modulesDir = $applicationDir.DIRECTORY_SEPARATOR.'modules';
$modules = array_diff(scandir($modulesDir), array('..', '.'));

$moduleConfigs = array_map(function ($module) use ($modulesDir) {
    return $modulesDir.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'codeception.yml';
}, $modules);

$existingModuleConfigs = array_filter(array_combine($modules, $moduleConfigs), function ($config) {
    return file_exists($config);
});

return array_merge_recursive(array(

    /*
    |--------------------------------------------------------------------------
    | Codeception Configurations
    |--------------------------------------------------------------------------
    |
    | This is where you add your Codeception configurations.
    |
    | Webception allows you to have access test suites for multiple applications.
    |
    | Place them in the order you want and they'll appear in the drop-down list
    | in the front-end. The first site in the list will become the default
    | site that's loaded on session load.
    |
    | Just add the site name and full path to the 'codeception.yml' below and you're set.
    |
    */

    'sites' => array_merge(
        ['All tests' => $applicationDir.DIRECTORY_SEPARATOR.'codeception.yml'],
        $existingModuleConfigs
    ),

    'modules' => $existingModuleConfigs,

    'groups' => [
        'AddAM',
        'AddAMFiles',
        'AddAMGallery',
        'AddAnnotation',
        'AddCrumbtrail',
        'AddHeading',
        'AddMainPhoto',
        'AddRow',
        'AddSearchResults',
        'AddTagMenu',
        'AMFiles',
        'AMGallery',
        'AmNews',
        'Amproducts',
        'Annotation',
        'archiveNews',
        'Article',
        'ArticleFill',
        'ArticleGroupSharing',
        'ArticleSharing',
        'ArticleUserSharing',
        'ArticleUserSharingCommentLink',
        'ArticleUserSharingCommentLinkSet',
        'ArticleUserSharingCommentLinkVisible',
        'ArticleUserSharingFillData',
        'ArticleUserSharingPrivate',
        'ArticleUserSharingPrivateSet',
        'ArticleUserSharingPrivateVisible',
        'ArticleUserSharingPublic',
        'ArticleUserSharingPublicVisible',
        'ArticleUserSharingReadLink',
        'ArticleUserSharingReadLinkSet',
        'ArticleUserSharingReadLinkVisible',
        'ArticleUserSharingRemovedLink',
        'ArticleUserSharingRemovedLinkSet',
        'ArticleUserSharingRemovedLinkVisible',
        'ArticleUserSharingUserComment',
        'ArticleUserSharingUserCommentSet',
        'ArticleUserSharingUserCommentVisible',
        'ArticleUserSharingUserEdit',
        'ArticleUserSharingUserEditSet',
        'ArticleUserSharingUserEditVisible',
        'ArticleUserSharingUserRead',
        'ArticleUserSharingUserReadSet',
        'ArticleUserSharingUserReadVisible',
        'Brand',
        'Calendar',
        'CalendarRecurrent',
        'CalendarShare',
        'CalendarWidget',
        'Client',
        'ClientAdd',
        'ClientFill',
        'ClientManualAdd',
        'ClientZone',
        'ClientZoneAccount',
        'ClientZoneAccountCheck',
        'ClientZoneAccountFill',
        'ClientZoneAccountFillComponent',
        'ClientZoneAccountFillCreate',
        'ClientZoneAccountFillPage',
        'ClientZoneAccountFillRows',
        'ClientZoneFill',
        'ClientZoneFillTemplate',
        'ClientZoneFillTemplateComponent',
        'ClientZoneFillTemplateCreate',
        'ClientZoneFillTemplateHeader',
        'ClientZoneFillTemplateRows',
        'ClientZoneMultipleComponents',
        'ClientZoneMultipleComponentsCheck',
        'ClientZoneMultipleComponentsFill',
        'ClientZoneMultipleComponentsFillComponent',
        'ClientZoneMultipleComponentsFillCreate',
        'ClientZoneMultipleComponentsFillPage',
        'ClientZoneMultipleComponentsFillRows',
        'ClientZoneRegistration',
        'ClientZoneRegistrationCheck',
        'ClientZoneRegistrationCheckPage',
        'ClientZoneRegistrationCheckUser',
        'ClientZoneRegistrationFill',
        'ClientZoneRegistrationFillComponent',
        'ClientZoneRegistrationFillCreate',
        'ClientZoneRegistrationFillPage',
        'ClientZoneRegistrationFillRows',
        'ComponentTemplate',
        'ConstructionContent',
        'ConstructionContentAddClientZone',
        'ConstructionContentAddComponentTemplate',
        'ConstructionContentAddCopyright',
        'ConstructionContentAddForm',
        'ConstructionContentAddFtxSearch',
        'ConstructionContentAddHeader',
        'ConstructionContentAddHeaderDial',
        'ConstructionContentAddLangSwitch',
        'ConstructionContentAddNavigationMenu',
        'ConstructionContentAddRows',
        'ConstructionContentAddSitemap',
        'ConstructionContentAddSubpage',
        'ConstructionContentAddTopPhoto',
        'ConstructionContentAddViceprace',
        'ConstructionContentCreate',
        'Contract',
        'ContractClient',
        'ContractEconomy',
        'ContractEconomyAddTimesheet',
        'ContractEconomyChangeContractInIssue',
        'ContractEconomyDeleteTimesheet',
        'ContractEconomyFillData',
        'ContractEconomyTimesheetTransfer',
        'ContractExistingIssue',
        'ContractIssue',
        'ContractList',
        'ContractNewIssue',
        'ContractQuickAction',
        'ContractQuickActions',
        'Copyright',
        'CrossUserVisibility',
        'CrossUserVisibilityUser',
        'CrossUserVisibilityUserComment',
        'CrossUserVisibilityUserCommentVisibility',
        'CrossUserVisibilityUserEdit',
        'CrossUserVisibilityUserEditSet',
        'CrossUserVisibilityUserEditVisibility',
        'cubeAlert',
        'cubeAMAnotation',
        'cubeAnchor',
        'cubeCitation',
        'cubeClientZone',
        'cubeConnectedPerson',
        'cubeCounter',
        'cubeCrumbtrail',
        'cubeDisqus',
        'cubeEmailNewsletter',
        'cubeFBLikeBox',
        'cubeFille',
        'cubeForm',
        'cubeFotogallery',
        'cubeFullTextSearch',
        'cubeGraphicAnotation',
        'cubeHeading',
        'cubeHorizontalLine',
        'cubeHorizontalSpace',
        'cubeInfobox',
        'cubeInstagram',
        'cubeKonvButton',
        'cubeMainPhoto',
        'cubeMap1',
        'cubeMap2',
        'cubeNavgationMenu',
        'cubeOwnCode',
        'cubePerson',
        'cubePicture',
        'cubePriceList',
        'cubeSharedText',
        'cubeSignPost',
        'cubeSiteMap',
        'cubeSocButtons',
        'cubeSocSites',
        'cubeSubPages',
        'cubeTagMenu',
        'cubeTweet',
        'cubeTwitterTimeline',
        'cubeVicePrace',
        'cubeVideo',
        'cubeWidgetNews',
        'cubeWidgetProducts',
        'cubeYTCanal',
        'DepartmentSharing',
        'detailNews',
        'Document',
        'DocumentAdd',
        'fastTest',
        'Form',
        'FtxSearch',
        'FuckUp',
        'GenericRequest',
        'GlobalSharedComponents',
        'GlobalSharedComponentsAddAM',
        'GlobalSharedComponentsAddAMFiles',
        'GlobalSharedComponentsAddAMGallery',
        'GlobalSharedComponentsAddAnnotation',
        'GlobalSharedComponentsAddCrombtrail',
        'GlobalSharedComponentsAddHeading',
        'GlobalSharedComponentsAddMainPhoto',
        'GlobalSharedComponentsAddSearchResults',
        'GlobalSharedComponentsAddTagMenu',
        'GlobalSharedComponentsCreate',
        'group1',
        'GroupSharing',
        'Header',
        'header',
        'HeaderDial',
        'HTML Slideshow',
        'HTMLSlideshow',
        'ImageSlideshow',
        'Issue',
        'IssueAssign',
        'IssueCreate',
        'IssueCrossUserVisibility',
        'IssueCrossUserVisibilityUser',
        'IssueCrossUserVisibilityUserComment',
        'IssueCrossUserVisibilityUserCommentVisibility',
        'IssueCrossUserVisibilityUserEdit',
        'IssueCrossUserVisibilityUserEditSet',
        'IssueCrossUserVisibilityUserEditVisibility',
        'IssueDepartmentSharing',
        'IssueGroupSharing',
        'IssuePositionSharing',
        'IssueSharing',
        'IssueState',
        'IssueUserSharing',
        'LangSwitch',
        'Menu',
        'NavigationMenu',
        'Parameter',
        'PositionSharing',
        'Request',
        'Reservation',
        'ReservationButtons',
        'ReservationCreate',
        'ReservationEdit',
        'ReservationFilter',
        'ReservationOverlapping',
        'ReservationSubject',
        'ReservationSubjectType',
        'SdilenePrvky',
        'SharedText',
        'Sharing',
        'Sitemap',
        'Slideshow',
        'Subpage',
        'System',
        'Tag',
        'Template',
        'TemplateContent',
        'TemplateContentAddAM',
        'TemplateContentAddAMFiles',
        'TemplateContentAddAMGallery',
        'TemplateContentAddAnnotation',
        'TemplateContentAddHTMLSlideshow',
        'TemplateContentAddImageSlideshow',
        'TemplateContentAddLangSwitch',
        'TemplateContentAddRow',
        'TemplateContentAddSlideshow',
        'TemplateContentAddSlisdeshow',
        'TemplateContentAddTopPhoto',
        'TemplateContentCreate',
        'TemplateLayout',
        'TemplateLayoutAddAM',
        'TemplateLayoutAddAMFiles',
        'TemplateLayoutAddAMGallery',
        'TemplateLayoutAddAnnotation',
        'TemplateLayoutAddComponentTemplate',
        'TemplateLayoutAddCopyright',
        'TemplateLayoutAddHeader',
        'TemplateLayoutAddHeaderDial',
        'TemplateLayoutAddHTMLSlideshow',
        'TemplateLayoutAddImageSlideshow',
        'TemplateLayoutAddLangSwitch',
        'TemplateLayoutAddRows',
        'TemplateLayoutAddSlideshow',
        'TemplateLayoutAddTopPhoto',
        'TemplateLayoutCreate',
        'TemplateProduct',
        'TepmlateLayoutAddSlideshow',
        'TextConstants',
        'thread1',
        'thread2',
        'thread3',
        'Timesheet',
        'TimesheetBlank',
        'TimesheetBtnAdd',
        'TimesheetIncorrectTime',
        'TimesheetNoActivity',
        'TimesheetNoIssueContract',
        'TimesheetNoIssueNote',
        'TimesheetOverlapping',
        'TimesheetValid',
        'TopPhoto',
        'twigy',
        'URLRedirect',
        'User',
        'UserAdd',
        'UserSharing',
        'UserSharingCommentLink',
        'UserSharingCommentLinkSet',
        'UserSharingCommentLinkVisible',
        'UserSharingFillData',
        'UserSharingPrivate',
        'UserSharingPrivateSet',
        'UserSharingPrivateVisible',
        'UserSharingPublic',
        'UserSharingPublicVisible',
        'UserSharingReadLink',
        'UserSharingReadLinkSet',
        'UserSharingReadLinkVisible',
        'UserSharingRemovedLink',
        'UserSharingRemovedLinkSet',
        'UserSharingRemovedLinkVisible',
        'UserSharingUserComment',
        'UserSharingUserCommentSet',
        'UserSharingUserCommentVisible',
        'UserSharingUserEdit',
        'UserSharingUserEditSet',
        'UserSharingUserEditVisible',
        'UserSharingUserRead',
        'UserSharingUserReadSet',
        'UserSharingUserReadVisible',
        'Viceprace',
        'Web',
        'web',
    ],

    /*
    |--------------------------------------------------------------------------
    | Execute Codeception as a PHP command
    |--------------------------------------------------------------------------
    */
    'run_php'        => TRUE,

    /*
    |--------------------------------------------------------------------------
    | Codeception Executable
    |--------------------------------------------------------------------------
    |
    | Codeception is installed as a dependancy of Webception via Composer.
    |
    | You might need to set 'sudo chmod a+x vendor/bin/codecept' to allow Apache
    | to execute the Codeception executable.
    |
    */

    'executable' => $applicationDir.
        DIRECTORY_SEPARATOR.'vendor'.
        DIRECTORY_SEPARATOR.'codeception'.
        DIRECTORY_SEPARATOR.'codeception'.
        DIRECTORY_SEPARATOR.'codecept',


    /*
    |--------------------------------------------------------------------------
    | You get to decide which type of tests get included.
    |--------------------------------------------------------------------------
    */

    'tests' => array(
        'webdriver'  => true,
        'phpbrowser' => true,
        'unit'       => false,
    ),

    /*
    |--------------------------------------------------------------------------
    | When we scan for the tests, we need to ignore the following files.
    |--------------------------------------------------------------------------
    */

    'ignore' => array(
        'WebGuy.php',
        'TestGuy.php',
        'CodeGuy.php',
        'AcceptanceTester.php',
        'FunctionalTester.php',
        'UnitTester.php',
        '_bootstrap.php',
        '.DS_Store',
    ),

    /*
    |--------------------------------------------------------------------------
    | Setting the location as the current file helps with offering information
    | about where this configuration file sits on the server.
    |--------------------------------------------------------------------------
    */

    'location'   => __FILE__,

    /*
    |--------------------------------------------------------------------------
    | Setting a Directory seperator in the configuration.
    | @todo Implement config driven seperator inplace of DIRECTORY_SEPERATOR
    |--------------------------------------------------------------------------
    */
    'DS'        => DIRECTORY_SEPARATOR,

    /*
    |--------------------------------------------------------------------------
    | Setting whether to pass additional run commands
    |--------------------------------------------------------------------------
    */
    'debug'        => FALSE,
    'steps'        => TRUE,
), $localConfig);
