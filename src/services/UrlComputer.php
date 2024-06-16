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

namespace jimstrike\siteimprove\services;

use Craft;
use craft\base\Component;
// use craft\elements\Entry;
// use craft\elements\Category;
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
     * @param int|null $siteId
     * 
     * @return string
     */
    public function cpUrl(Event $event, ?int $siteId = null): string
    {
        if (!$event instanceof TemplateEvent) {
            return '';
        }

        $routeParams = Craft::$app->getUrlManager()->getRouteParams();
        $elementId = $routeParams['elementId'] ?? ($routeParams['productId'] ?? null);

        if (!$elementId) {
            return '';
        }

        $element = Craft::$app->getElements()->getElementById($elementId, null, $siteId);

        if (!$element) {
            return '';
        }

        if (!empty($element->uri)) {
            return $this->_computeUrl($siteId, $element->uri);
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
