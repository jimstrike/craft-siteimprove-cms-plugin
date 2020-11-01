<?php
/**
 * "Siteimprove CMS Plugin" plugin for Craft CMS 3.x
 *
 * Siteimprove data right where you need it.
 * The Siteimprove plugin bridges the gap between Craft CMS and the Siteimprove Intelligence Platform. 
 * Thanks to the seamless integration, you are now able to put your Siteimprove results to use where 
 * they are most valuable - during your content creation and editing process.
 *
 * @link      https://github.com/jimstrike
 * @copyright Copyright (c) Dhimiter Karalliu
 */

namespace jimstrike\siteimprove\twigextensions;

use Craft;
use jimstrike\siteimprove\Plugin;

/**
 * @author  Dhimiter Karalliu
 * @package Siteimprove CMS Plugin
 * @since   1.0.0
 */
class TwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     * 
     * @return string
     */
    public function getName(): string
    {
        return Plugin::$plugin->name;
    }

    /**
     * @inheritdoc
     * 
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter(Plugin::$plugin->handle . '_is_numeric', function($value) { 
                return  \is_numeric($value); 
            }),
        ];
    }

    /**
     * @inheritdoc
     * 
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction(Plugin::$plugin->handle . '_asset', [$this, 'assetFunction']),
        ];
    }

    /**
     * Asset Published Url
     * 
     * @param string $resourcePath
     * 
     * @return string 
     */
    public function assetFunction(string $resourcePath): string
    {
        return Plugin::assetsBaseUrl() . '/' . \ltrim($resourcePath, '/');
    }
}
