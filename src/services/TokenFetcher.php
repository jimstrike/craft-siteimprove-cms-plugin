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
use jimstrike\siteimprove\Plugin;

/**
 * @author  Dhimiter Karalliu
 * @package Siteimprove CMS Plugin
 * @since   1.0.0
 */
class TokenFetcher extends Component
{
    /**
     * Siteimprove endpoint
     * 
     * @var string
     */
    const TOKEN_ENDPOINT = 'https://my2.siteimprove.com/auth/token?cms=Craft+CMS';

    // Public Methods
    // =========================================================================

    /**
     * Get token endpoint
     * 
     * @return string
     */
    public function getEndpoint(): string
    {
        return self::TOKEN_ENDPOINT . '+' . Craft::$app->getInfo()->version;
    }

    /**
     * Generate token
     * 
     * @return string
     */
    public function generate(): string
    {
        $client = new \GuzzleHttp\Client();
        
        $response = $client->request('GET', $this->getEndpoint(), [
            'User-Agent' => 'Craft CMS Plugin',
            'Accept' => 'application/json'
        ]);

        if ($response->getStatusCode() == 200) {
            $data = \json_decode($response->getBody()->getContents());

            Craft::info(Plugin::t($this->_logClientResponse($response, 'Siteiprove token generated successfully'), []), __METHOD__);

            return $data->token;
        }

        $message = Plugin::t('Unable to generate Siteimprove token.');

        Craft::error(Plugin::t($this->_logClientResponse($response, $message), []), __METHOD__);

        return $message;
    }

    // Private Methods
    // =========================================================================

    /**
     * Log client response
     * 
     * @param \GuzzleHttp\Psr7\Response $response
     * @params string $message
     * 
     * @return string
     */
    private function _logClientResponse(\GuzzleHttp\Psr7\Response $response, string $message): string
    {
        return \json_encode([
            (Plugin::$plugin->handle) => Plugin::$plugin->name,
            'message' => $message,
            'status' => \implode(' ', [$response->getStatusCode(), $response->getReasonPhrase()]),
        ]);
    }
}
