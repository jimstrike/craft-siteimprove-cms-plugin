/**
 * Siteimprove CMS Plugin plugin for Craft CMS 4.x
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
 * Siteimprove siteimprove.js 
 * https://developer.siteimprove.com/cms-plugin-integration-cookbook/
 */
window.addEventListener('load', function(e) {
    SiteimproveUI.init();
}, false);

const SiteimproveUI = function() {
    const plugin = JSON.parse(window._SITEIMRPOVE_CMS_PLUGIN);

    // Debug
    function _log(message) {
        console.log({
            'siteimprove': message,
            'plugin': plugin
        });
    }

    // Method to call _si functions
    function _call(method, url, callback) {

        // debug
        if (plugin.devMode) {
            _log('Calling Siteimprove `' + method + '` method with URL: `' + plugin.url + '`');
        }

        // callback
        let _callback = function () {
            if (typeof callback === 'function') {
                callback();
            }
        };

        // siteimprove
        let _si = window._si || [];
        
        // methods
        if (method !== 'clear') {
            _si.push([method, url, plugin.token, _callback]);
        } 
        else {
            _si.push([method, _callback]);
        }
    }

    // Call input method
    function input(url, callback) {
        _call('input', url, callback);
    }

    // Call domain method
    function domain(url, callback) {
        _call('domain', url, callback);
    }

    // Call recheck method
    function recheck(url, callback) {
        _call('recheck', url, callback);
    }

    // Call recrawl method
    function recrawl(url, callback) {
        _call('recrawl', url, callback);
    }

    // Call clear method
    function clear(callback) {
        _call('clear', '', callback);
    }

    // Add script tag
    function addScript(url, callback) {
        var s = document.createElement('script');
            s.src = url; s.async = true; s.onload = callback;
        //(document.getElementsByTagName('head')[0] || document.documentElement).appendChild(s);
        document.body.appendChild(s);
    }

    // Init siteimprove
    function init() {
        let src = plugin.script || 'https://cdn.siteimprove.net/cms/overlay.js';
        
        addScript(src, function() {
            
            // call 'input'
            if (plugin.url && plugin.url != '') {
                input(plugin.url, function() {
                    // callback
                });
                
                return;
            }
            
            // call 'domain'
            domain(plugin.url, function() {
                // callback
            });
        });
    }

    return {
        init: init
    };
}();
