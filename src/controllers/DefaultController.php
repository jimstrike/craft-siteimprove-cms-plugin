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

namespace jimstrike\siteimprove\controllers;

use Craft;
use craft\helpers\UrlHelper;

use yii\web\Response;
//use yii\web\ForbiddenHttpException;

use jimstrike\siteimprove\Plugin;

/**
 * @author  Dhimiter Karalliu
 * @package Siteimprove CMS Plugin
 * @since   1.0.0
 */
class DefaultController extends BaseController
{

    // Protected Properties
    // =========================================================================

    /**
     * The actions must be in 'kebab-case'
     * @var bool|array Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected array|int|bool $allowAnonymous = false;

    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();
    }

    /**
     * Index action
     *
     * @return Response
     */
    public function actionIndex(): Response
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('siteimprove/settings'));
    }

    /**
     * Settings action
     *
     * @return Response
     */
    public function actionSettings(): Response
    {
        $sites = Craft::$app->getSites();
        
        $primarySite = $sites->getPrimarySite();
        $siteHandle = Craft::$app->getRequest()->getQueryParam('site') ?? $primarySite->handle;
        $currentSite = $sites->getSiteByHandle($siteHandle) ?? $primarySite;

        $pluginVars = $this->_pluginVars();

        return $this->renderTemplate(Plugin::$plugin->handle . '/_default/settings', [
            'plugin' => $pluginVars,
            'currentSite' => $currentSite,
        ]);
    }

    /**
     * Save Settings action
     *
     * @return Response|null
     */
    public function actionSaveSettings()
    {
        $this->requirePostRequest();

        $params = Craft::$app->getRequest()->getBodyParams();
        
        $siteId = $params['siteId'];
        $data = $params['settings'];

        $settings = Plugin::getInstance()->getSettings();

        foreach ($data as $field => $value) {
            $settings->$field = $settings->makeValue($field, $value, $siteId);
        }

        $pluginSettingsSaved = Craft::$app->getPlugins()->savePluginSettings(Plugin::getInstance(), $settings->toArray());

        Craft::$app->getSession()->setNotice(Plugin::t('global.settings.saved'));

        return $this->redirectToPostedUrl();
    }

    /**
     * Generate Siteimprove token
     *
     * @return Response
     */
    public function actionJsonTokenGenerate(): Response
    {
        $token = Plugin::$plugin->tokenFetcher->generate();

        return $this->asJson([
            'token' => $token,
        ]);
    }

    /**
     * Plugin properties and methods 
     * we want to be accessible in templates
     *
     * @return array
     */
    private function _pluginVars(): array
    {
        return [
            'name' => Plugin::$plugin->name,
            'handle' => Plugin::$plugin->handle,
            'settings' => Plugin::$plugin->getSettings()
        ];
    }
}
