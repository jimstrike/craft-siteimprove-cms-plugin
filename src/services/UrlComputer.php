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

namespace jimstrike\siteimprove\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\elements\Category;
use craft\events\TemplateEvent;
use craft\helpers\ElementHelper;
use craft\helpers\UrlHelper;

use yii\base\Event;

use jimstrike\siteimprove\Plugin;

/**
 * @author  Dhimiter Karalliu
 * @package Siteimprove CMS Plugin
 * @since   1.0.0
 */
class UrlComputer extends Component
{
    /**
     * Compute CP URL
     *  
     * @param Event $event
     * 
     * @return string
     */
    public function cpUrl(Event $event): string
    {
        if (!$event instanceof TemplateEvent) {
            return '';
        }

        $variables = $event->variables ?? [];
        
        if (!$variables) {
            return '';
        }

        $siteId = $variables['site']->id ?? Craft::$app->getSites()->getCurrentSite()->id;
        
        // entry
        $entry = $variables['entry'] ?? null;
        if ($entry instanceof Entry && !empty((string)($entry->uri ?? ''))) {
            return $this->_computeUrl($siteId, $entry->uri);
        }

        // category
        $category = $variables['category'] ?? null;
        if ($category instanceof Category && !empty((string)($category->uri ?? ''))) {
            return $this->_computeUrl($siteId, $category->uri);
        }
        
        // product
        $commerce = Craft::$app->plugins->getPlugin('commerce', false);
        if (!empty($commerce) && $commerce->isInstalled) {
            $product = $variables['product'] ?? null;
            
            if ($product instanceof \craft\commerce\elements\Product && !empty((string)($product->uri ?? ''))) {
                return $this->_computeUrl($siteId, $product->uri);
            }
        }

        return '';
    }

    /**
     * Compute Site URL
     * 
     * @param int $siteId default
     * 
     * @return string
     */
    public function siteUrl(int $siteId = null): string
    {
        if (!$siteId) {
            return Plugin::baseRequestUrlAndFullPath();
        }

        $request = Craft::$app->getRequest();

        return $this->_computeUrl($siteId, $request->getFullPath());
    }

    // Private Methods
    // =========================================================================

    /**
     * Compute URL
     * 
     * @param int $siteId
     * @param string $uri
     * 
     * @return string
     */
    private function _computeUrl(int $siteId, string $uri): string
    {
        $isTempUri = $this->_isTempUri($uri);

        if ($isTempUri) {
            return '';
        }

        $settings = Plugin::$plugin->getSettings();
        $homeUris = [\craft\base\Element::HOMEPAGE_URI, '__home__'];

        if (\in_array($uri, $homeUris)) {
            $uri = \str_replace($homeUris, '', $uri);
        }

        $uri = \trim($uri, '/');

        // @todo: pass querystring params to url?
        
        $useTestBaseUrl = $settings->getUseTestBaseUrl($siteId);
        
        if ($useTestBaseUrl) {
            $siteBaseUrl = \rtrim(Craft::$app->getSites()->getSiteById($siteId)->getBaseUrl(), '/');
            $testBaseUrl = \rtrim($settings->getTestBaseUrl($siteId), '/');

            if ($testBaseUrl != $siteBaseUrl) {
                return $testBaseUrl . '/' . $uri;
            }
        }

        return UrlHelper::siteUrl($uri, null, null, $siteId);
    }

    /**
     * Returns whether the given uri is temporary.
     * 
     * @param string $uri
     * 
     * @return bool
     */
    private function _isTempUri(string $uri): bool
    {
        $pieces = \array_filter(\explode('/', $uri));
        $slug = \end($pieces);

        return ElementHelper::isTempSlug($slug);
    }
}
