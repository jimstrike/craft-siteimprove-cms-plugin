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

namespace jimstrike\siteimprove\models;

use Craft;
use craft\base\Model;
use craft\helpers\DateTimeHelper;

use jimstrike\siteimprove\Plugin;
use jimstrike\siteimprove\helpers as Helpers;

/**
 * @author  Dhimiter Karalliu
 * @package Siteimprove CMS Plugin
 * @since   1.0.0
 */
class Settings extends Model
{
    // Public
    public $enabled;
    public $siteEnabled;
    public $testBaseUrl;
    public $useTestBaseUrl;
    public $token;
    public $savedCraftVersion;

    // Getters and Setters
    // =========================================================================

    /**
     * @param int|null $siteId default
     * 
     * @return bool
     */
    public function getEnabled(int $siteId = null): bool
    {
        $setting = $this->_getSetting('enabled', $siteId);
        
        return (bool)$setting ?: false;
    }

    /**
     * @param int|null $siteId default
     * 
     * @return bool
     */
    public function getSiteEnabled(int $siteId = null): bool
    {
        $setting = $this->_getSetting('siteEnabled', $siteId);
        
        return (bool)$setting ?: false;
    }

    /**
     * @param int|null $siteId default
     * 
     * @return string
     */
    public function getTestBaseUrl(int $siteId = null): string
    {
        $setting = $this->_getSetting('testBaseUrl', $siteId);
        $setting = \filter_var($setting, FILTER_SANITIZE_URL);

        if (!\filter_var($setting, FILTER_VALIDATE_URL)) {
            return Craft::$app->getSites()->getSiteById($siteId)->getBaseUrl();
        }
        
        return $setting;
    }

    /**
     * @param int|null $siteId default
     * 
     * @return bool
     */
    public function getUseTestBaseUrl(int $siteId = null): bool
    {
        $setting = $this->_getSetting('useTestBaseUrl', $siteId);
        
        return (bool)$setting ?: false;
    }

    /**
     * @param int|null $siteId default
     * 
     * @return string
     */
    public function getToken(int $siteId = null): string
    {
        $setting = $this->_getSetting('token', $siteId);
        
        return $setting ?: '';
    }

    /**
     * @param int|null $siteId default
     * 
     * @return string
     */
    public function getSavedCraftVersion(int $siteId = null): string
    {
        $setting = $this->_getSetting('savedCraftVersion', $siteId);
        
        return $setting ?: ''; // Craft::$app->getInfo()->version
    }

    // Helper set methods
    // =========================================================================

    /**
     * Set array value for property
     * 
     * @param string $field
     * @param mixed $value
     * @param int $siteId default
     * 
     * @return array
     * $this->pageId = [
     *     ['siteId' => 'value'],
     *     ['siteId' => 'value']
     * ]
     */
    public function makeValue(string $field, $value, int $siteId = 1): array
    {
        // Sanitize value
        $value = $this->_sanitizeValue($field, $value, $siteId);

        $base = \is_array($this->$field) ? $this->$field : [];
        
        $replace = [($siteId) => (\is_string($value) ? \trim($value) : ($value ?? ''))];

        $a = \array_replace($base, $replace) ?? (array)$this->$field;
        
        \ksort($a);

        return $a;
    }

    // Private methods
    // =========================================================================

    /**
     * @param string $field
     * @param mixed $value
     * @param int $siteId
     * 
     * @return mixed
     */
    private function _sanitizeValue(string $field, $value, int $siteId = 1)
    {
        if ($field == 'token') {
            if (empty($value) || $this->getSavedCraftVersion($siteId) != Craft::$app->getInfo()->version) {
                $value = Plugin::$plugin->tokenFetcher->generate();
            }
        }

        if ($field == 'testBaseUrl') {
            $value = \filter_var($value, FILTER_SANITIZE_URL);

            if (!\filter_var($value, FILTER_VALIDATE_URL)) {
                $value = Craft::$app->getSites()->getSiteById($siteId)->getBaseUrl();
            }
        }

        // --

        return $value;
    }

    /**
     * @param string $setting
     * @param int|null $siteId default
     * 
     * @return mixed
     */
    private function _getSetting(string $setting, int $siteId = null)
    {
        if (empty($siteId)) {
            $siteId = Craft::$app->getSites()->getCurrentSite()->id ?? null;
        }

        $configs = Craft::$app->getConfig()->getConfigFromFile('siteimprove');
        
        if (isset($configs[$siteId][$setting])) {
            return $configs[$siteId][$setting];
        }

        if (isset($configs[$setting])) {
            return $configs[$setting];
        }

        return $this->$setting[$siteId] ?? '';
    }
}