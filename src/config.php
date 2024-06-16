<?php
/**
 * "Siteimprove CMS Plugin" plugin for Craft CMS 4.x|5.x
 *
 * Siteimprove data right where you need it.
 * The Siteimprove plugin bridges the gap between Craft CMS and the Siteimprove Intelligence Platform. 
 * Thanks to the seamless integration, you are now able to put your Siteimprove results to use where 
 * they are most valuable - during your content creation and editing process.
 *
 * @link      https://github.com/jimstrike
 * @copyright Copyright (c) Dhimiter Karalliu
 */

/**
 * Siteimprove CMS Plugin config.php
 *
 * This file exists only as a template for the "Siteimprove CMS Plugin" settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to '<craft_project>/config' as 'siteimprove.php'
 * and make your changes there to override default settings.
 *
 * Once copied to '<craft_project>/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

 // Same settings on all sites
return [
    /**
     * Determines if the plugin should be enabled. 
     * This will embed "Siteimprove CMS Plugin" in control panel next to entries, categories and Commerce products.
     * 
     * @var bool
     */ 
    'enabled' => false,

    /**
     * Determines if the plugin should appear on all your site pages (frontend) when it is enabled. 
     * Only available while logged in as admin, can access control panel and have permissions to use this plugin. 
     * If you want it to work for control panel running on a subdomain then set `defaultCookieDomain` to work for all subdomains. 
     * 
     * @var bool
     */
    'siteEnabled' => false,

    /**
     * In environments other the production you can override site base URL with the one in production 
     * in order to test against Siteimprove. Siteimprove has access only to the public (production) URL. 
     * Defaults to the URL of the site defined in Settings → Sites → Your default site.
     * 
     * @var string
     */
    'testBaseUrl' => '',

    /**
     * Enable this if you want to test against the URL defined in 'testBaseUrl'.
     * 
     * @var bool
     */
    'useTestBaseUrl' => false,

    /**
     * Siteimprove CMS Plugin token.
     * Login to CP and generate a new token by visiting this URI:
     * `/admin/siteimprove/json/token/generate`
     * 
     * @var string
     */
    'token' => 'Insert a valid token here',

    /**
     * Craft CMS current version
     * 
     * @var string
     */
    'savedCraftVersion' => \Craft::$app->getInfo()->version,
];

/*
// Multi-site settings
// return [
//     // Site with ID: 1
//     (1) => [
//         'enabled' => true,
//         // ...
//     ],

//     // Site with ID: 2
//     (2) => [
//         'enabled' => false,
//         // ...
//     ]
// ];
*/
