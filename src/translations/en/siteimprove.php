<?php
/**
 * "Siteimprove CMS Plugin" plugin for Craft CMS 4.x
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
 * Translation: en 
 */
return [

    // PLUGIN
    // =========================================================================

    'plugin.name' => 'Siteimprove CMS Plugin',
    'plugin.slogan' => 'Siteimprove data right where you need it',

    // GLOBAL
    // =========================================================================

    'global.settings.saved' => 'Settings saved.',
    'global.save' => 'Save',
    'global.please_wait' => 'Please wait',
    'global.token.generate_new_token' => 'Generate new token',
    'global.token.token_generated' => 'Token generated',
    'global.documentation' => 'Documentation',
    'global.docs' => 'Docs',

    // SUBNAV
    // =========================================================================

    'subnav.getting_started.heading' => 'Documentation',
    'subnav.settings.heading' => 'Site settings',

    'subnav.getting_started.label' => 'Getting started',
    'subnav.settings.label' => 'Settings',

    // SETTINGS
    // =========================================================================

    // Settings label
    'settings.token.label' => 'Siteimprove token',
    'settings.enabled.label' => 'Enable {plugin.name}?',
    'settings.site_enabled.label' => 'Enable {plugin.name} on your site pages?',
    'settings.test_base_url.label' => 'Test base URL',
    'settings.use_test_base_url.label' => 'Use "{settings.test_base_url.label}"?',

    // Settings instructions
    'settings.token.instructions' => 'Siteimprove token will autogenerate when you save your settings.',
    'settings.enabled.instructions' => 'Determines if the plugin should be enabled. This will embed "{plugin.name}" in control panel next to entries, categories and Commerce products.',
    'settings.site_enabled.instructions' => 'Determines if the plugin should appear on all your site pages (frontend) when it is enabled. Only available while logged in as admin, can access control panel and have permissions to use this plugin. If you want it to work for control panel running on a subdomain then set `defaultCookieDomain` to work for all subdomains.',
    'settings.test_base_url.instructions' => 'In environments other the production you can override site base URL with the one in production in order to test against Siteimprove. Siteimprove has access only to the public (production) URL. Defaults to the URL of the site defined in Settings → Sites → {site.name} → `{site.baseUrl}`.',
    'settings.use_test_base_url.instructions' => 'Enable this if you want to test against the URL above.',

    // Settings footnotes

    // Settings errors
    'settings.token.error.blank' => 'Siteimprove token cannot be blank. Click `Save` to generate a new token.',

    // Settings warnings
    'settings.config.warning' => 'This is being overridden by the `{setting}` setting in the `{file}` file.',
    'settings.saved_craft_version.warning.mismatch' => 'Craft CMS has been updated to a new version. Click `Save` to generate a new token.',

    // PAGE
    // =========================================================================

    'page.getting_started.title' => 'Getting started',
    'page.settings.title' => 'Settings',
    'page.settings.footnote' => 'In order to use the {plugin.name}, you will need to be a Siteimprove customer. Not a customer yet? Have a look: {siteimprove.url}.',

    // TEXT
    // =========================================================================

    // EXCEPTION
    // =========================================================================

    'exception.forbidden.access' => 'You are not allowed to access this page.',
    'exception.forbidden.action' => 'You are not allowed to perform this action.',

    // PERMISSIONS
    // =========================================================================

    'permissions.embed' => 'Embed and allow usage',

];
