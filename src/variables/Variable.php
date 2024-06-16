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

namespace jimstrike\siteimprove\variables;

use Craft;
use jimstrike\siteimprove\Plugin;
use jimstrike\siteimprove\models\Settings;

/**
 * @author  Dhimiter Karalliu
 * @package Siteimprove CMS Plugin
 * @since   1.0.0
 */
class Variable
{
    // Public Methods
    // =========================================================================

    /**
     * @return string 
     */
    public function getName(): string
    {
        $name = Plugin::$plugin->name;

        return $name;
    }

    /**
     * Settings
     * 
     * @return Settings model
     */
    public function settings(): Settings
    {
        return Plugin::$plugin->getSettings();
    }

    /**
     * Asset Published Url
     * 
     * @param string $resourcePath
     * 
     * @return string 
     */
    public function asset(string $resourcePath): string
    {
        return Plugin::assetsBaseUrl() . '/' . \ltrim($resourcePath, '/');
    }

    /**
     * Composer
     * 
     * @return string 
     */
    public function composer(): array
    {
        return Plugin::composer();
    }
}
