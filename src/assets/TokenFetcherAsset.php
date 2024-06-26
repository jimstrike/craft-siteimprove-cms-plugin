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

namespace jimstrike\siteimprove\assets;

use craft\web\AssetBundle;
use yii\web\JqueryAsset;

use jimstrike\siteimprove\Plugin;
use jimstrike\siteimprove\assets\RuntimeAsset;

/**
 * @author  Dhimiter Karalliu
 * @package Siteimprove CMS Plugin
 * @since   1.0.0
 */
class TokenFetcherAsset extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = Plugin::assetsNsPrefix();

        // define the dependencies
        $this->depends = [
            JqueryAsset::class,
            RuntimeAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'token_fetcher.js',
        ];

        $this->css = [
            'token_fetcher.css'
        ];

        parent::init();
    }
}
