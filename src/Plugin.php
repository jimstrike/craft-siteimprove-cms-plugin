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

namespace jimstrike\siteimprove;

use Craft;
use craft\services\Plugins;
//use craft\events\PluginEvent;
use craft\events\RegisterUrlRulesEvent;
//use craft\events\RegisterCpNavItemsEvent;
use craft\web\UrlManager;
use craft\web\View;
//use craft\web\twig\variables\Cp;
use craft\web\twig\variables\CraftVariable;
use craft\helpers\UrlHelper;

use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;

use yii\base\Event;

use jimstrike\siteimprove\models\Settings;
use jimstrike\siteimprove\services\TokenFetcher;
use jimstrike\siteimprove\services\UrlComputer;
use jimstrike\siteimprove\twigextensions\TwigExtension;
use jimstrike\siteimprove\variables\Variable;

use jimstrike\siteimprove\assets\SiteimproveAsset;

/**
 * @author  Dhimiter Karalliu
 * @package Siteimprove CMS Plugin
 * @since   1.0.0
 */
class Plugin extends \craft\base\Plugin
{
    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Plugin::$plugin
     *
     * @var Plugin
     */
    public static $plugin;

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @inheritdoc
     * @var string
     */
    public string $schemaVersion = '2.0.0';

    /**
     * @inheritdoc
     * @var bool
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritdoc
     * @var bool
     */
    public bool $hasCpSection = true;

    /**
     * @var string
     */
    const ASSETS_NS_PREFIX = '@jimstrike/siteimprove/assets/dist';

    /**
     * @var string
     */
    const SCRIPT_OVERLAY = 'https://cdn.siteimprove.net/cms/overlay.js';

    /**
     * @inheritdoc
     * 
     * @return void
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Disable cp section if no admin changes allowed
        if (!Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
            $this->hasCpSection = false;
        }

        // Register components and other stuff
        $this->_registerComponents();
        $this->_registerTwigExtensions();
        $this->_registerVariables();
        $this->_registerCpRoutes();
        $this->_registerBeforeRenderPageTemplate();
        $this->_registerPermisions();

        // Logging - We're loaded
        Craft::info(self::t('{name} plugin loaded', ['name' => $this->name]), __METHOD__);
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem(): array
    {
        $item = parent::getCpNavItem();

        $item['label'] = self::t('Siteimprove');
        $item['badgeCount'] = \call_user_func(function() {
            $count = 0;
            $settings = $this->getSettings();
            $sites = Craft::$app->sites->getAllSites();

            foreach ($sites as $site) {
                if ($settings->getEnabled($site->id)) {
                    if (empty($settings->getToken($site->id)) || $settings->getSavedCraftVersion($site->id) != Craft::$app->getInfo()->version) {
                        $count += 1;
                    }
                }
            }

            return $count;
        });

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl($this->handle . '/settings'));
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     * 
     * @return Settings|null
     */
    protected function createSettingsModel(): ?craft\base\Model
    {
        $settings = new Settings();
        
        return $settings;
    }

    // Static helpers
	// =========================================================================

    /**
     * Plugin's t() method
     * Plugin::t('message to be translated')
     * 
     * @var string $message
     * @var array $params
     * 
     * @return string
     */
	public static function t(string $message, array $params = [], string $language = null): string
    {
        return Craft::t(self::$plugin->handle, $message, $params, $language);
    }

    /**
     * Base request  URL and full path
     * 
     * @return string
     */
    public static function baseRequestUrlAndFullPath(): string
    {
        //return \yii\helpers\Url::base(true) . Craft::$app->getRequest()->getUrl();
        // return \rtrim(UrlHelper::baseRequestUrl(), '/') . '/' . \ltrim(Craft::$app->getRequest()->getFullPath(), '/');
        return \rtrim(UrlHelper::baseUrl(), '/') . '/' . \ltrim(Craft::$app->getRequest()->getFullPath(), '/');
    }

    /**
     * Assets ns prefix
     * 
     * @return string
     */
    public static function assetsNsPrefix(): string
    {
        return \rtrim(self::ASSETS_NS_PREFIX, '/');
    }

    /**
     * Asset base url
     * 
     * @return string
     */
    public static function assetsBaseUrl(): string
    {
        $nsPrefix = self::assetsNsPrefix();

        return Craft::$app->assetManager->getPublishedUrl($nsPrefix);
    }

    /**
     * Composer
     * 
     * @return array
     */
    public static function composer(): array
    {
        try {
            $composer = dirname(__FILE__) . '/../composer.json';
            $json = file_get_contents($composer);

            return json_decode($json, true);
        } 
        catch (\Exception $e) {
            return [];
        }
    }

    // Private methods
    // =========================================================================

    /**
     * Register components
     * 
     * @return void
     */
    private function _registerComponents(): void
    {
        $this->setComponents([
            'tokenFetcher' => TokenFetcher::class,
            'urlComputer' => UrlComputer::class,
        ]);
    }

    /**
     * Register twig extensions
     * 
     * @return void
     */
    private function _registerTwigExtensions(): void
    {
        Craft::$app->view->registerTwigExtension(new TwigExtension());
    }

    /**
     * Register variables
     * 
     * @return void
     */
    private function _registerVariables(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->set($this->handle, Variable::class);
        });
    }

    /**
     * Register CP routes
     * 
     * @return void
     */
    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['siteimprove'] = 'siteimprove/default/index';
            $event->rules['siteimprove/index'] = 'siteimprove/default/index';
            $event->rules['siteimprove/settings'] = 'siteimprove/default/settings';
            // Json
            $event->rules['siteimprove/json/token/generate'] = 'siteimprove/default/json-token-generate';
        });
    }

    /**
     * Register before render template
     * 
     * @return void
     */
    private function _registerBeforeRenderPageTemplate(): void
    {
        Event::on(View::class, View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE, function(Event $event) {
            Craft::debug('View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE', __METHOD__);
            
            $user = Craft::$app->getUser();
            $request = Craft::$app->getRequest();
            $embed = $this->__embed($user, $request);

            if ($embed) {
                $site = Craft::$app->getSites()->getCurrentSite();
                $settings = $this->getSettings();

                $siteId = $event->variables['site']->id ?? $site->id;

                // CP
                if ($request->getIsCpRequest() && $settings->getEnabled($siteId)) {
                    $this->__registerAssetBundle($settings->getToken($siteId), $this->urlComputer->cpUrl($event));
                }

                // Site
                if ($request->getIsSiteRequest() && $settings->getEnabled($siteId) && $settings->getSiteEnabled($siteId)) {
                    $this->__registerAssetBundle($settings->getToken($siteId), $this->urlComputer->siteUrl($siteId));
                }
            }
        });
    }

    /**
     * Register end body
     * 
     * @return void
     */
    private function _registerEndBody(): void
    {
        /*Event::on(View::class, View::EVENT_END_BODY, function() {
            // @todo
        });*/
    }

    /**
     * Register before install
     * 
     * @return void
     */
    private function _registerBeforeInstall(): void
    {
        /*Event::on(Plugins::class, Plugins::EVENT_BEFORE_INSTALL_PLUGIN, function(PluginEvent $event) {
            if ($event->plugin === $this) {
                // We were just installed
            }
        });*/
    }

    /**
     * Register after install
     * 
     * @return void
     */
    private function _registerAfterInstall(): void
    {
        /*Event::on(Plugins::class, Plugins::EVENT_AFTER_INSTALL_PLUGIN, function(PluginEvent $event) {
            if ($event->plugin === $this) {
                // We were just installed
            }
        });*/
    }

    /**
     * Register permission
     * 
     * @return void
     */
    private function _registerPermisions(): void
    {
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function(RegisterUserPermissionsEvent $event) {
                $event->permissions[$this->name] = [
                    'embedSiteimprove' => [
                        'label' => self::t('permissions.embed'),
                    ],
                ];
            }
        );
    }

    /**
     * Register asset bundle
     * 
     * @param string $token
     * @param string $url
     * 
     * @return void
     */
    private function __registerAssetBundle(string $token = '', string $url = ''): void
    {
        $json = json_encode([
            'token' => $token, 
            'url' => $url, 
            'script' => self::SCRIPT_OVERLAY,
            'devMode' => Craft::$app->config->general->devMode
        ]);

        $view = Craft::$app->getView();
                
        // Register js window global variable `_SITEIMRPOVE_CMS_PLUGIN`
        $view->registerJs("window._SITEIMRPOVE_CMS_PLUGIN='{$json}'", View::POS_HEAD);
        
        // Register assets
        $view->registerAssetBundle(SiteimproveAsset::class);
    }

    /**
     * Check if it can embed
     * 
     * @param $user
     * @param $request
     * 
     * @return bool
     */
    public function __embed(\craft\web\User $user, \craft\web\Request $request): bool
    {
        return (
            !$user->getIsGuest() && ($user->getIsAdmin() || ($user->checkPermission('accessCp') && $user->checkPermission('embedSiteimprove') )) &&
            !$request->getIsConsoleRequest() && !$request->getIsAjax() && !$request->getIsPreview()
        );
    }
}
