<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package woorefill-extension
 * @version 1.0.x
 */

namespace WooRefill\App;

/**
 * Class Updater
 */
class Updater
{
    private $slug; // plugin slug
    private $pluginData; // plugin data
    private $username = 'ynloultratech'; // GitHub username
    private $repo = 'woorefill-extension'; // GitHub repo name
    private $pluginFile; // __FILE__ of our plugin
    private $githubAPIResult; // holds data from GitHub
    private $accessToken; // GitHub private repo token

    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

    /**
     * Get information regarding our plugin from WordPress
     */
    private function initPluginData()
    {
        $this->slug = plugin_basename($this->pluginFile);
        $this->pluginData = get_plugin_data($this->pluginFile);
    }

    /**
     * Get information regarding our plugin from GitHub
     */
    private function getRepoReleaseInfo()
    {
        // Only do this once
        if (!empty($this->githubAPIResult)) {
            return;
        }

        // Query the GitHub API
        $url = "https://api.github.com/repos/{$this->username}/{$this->repo}/releases/latest";

        // We need the access token for private repos
        if (!empty($this->accessToken)) {
            $url = add_query_arg(["access_token" => $this->accessToken], $url);
        }

        $response = wp_remote_get($url, ['timeout' => 60]);

        if ($response instanceof \WP_Error) {
            return $response;
        }

        // Get the results
        $this->githubAPIResult = @json_decode(isset($response['body']) ? $response['body'] : null);
    }

    /**
     * Push in plugin version information to get the update notification
     *
     * @param $transient
     *
     * @return mixed
     */
    public function setTransitent($transient)
    {
        if (empty($transient->checked)) {
         //return $transient;
        }

        // Get plugin & GitHub release information
        $this->initPluginData();
        $this->getRepoReleaseInfo();

        // Check the versions if we need to do an update
        $doUpdate = version_compare($this->githubAPIResult->tag_name, $transient->checked[$this->slug]);

        // Update the transient to include our updated plugin data
        if ($doUpdate === 1) {
            // Release download zip file
            $package = null;
            if ($this->githubAPIResult->assets) {
                $asset = current($this->githubAPIResult->assets);
                $package = $asset->browser_download_url;
            }

            $obj = new \stdClass();
            $obj->slug = $this->slug;
            $obj->new_version = $this->githubAPIResult->tag_name;
            $obj->url = $this->pluginData["PluginURI"];
            $obj->package = $package;
            $transient->response[$this->slug] = $obj;
        }

        // code here
        return $transient;
    }

    /**
     * Push in plugin version information to display in the details lightbox
     *
     * @param $false
     * @param $action
     * @param $response
     *
     * @return bool
     */
    public function setPluginInfo($false, $action, $response)
    {
        $this->initPluginData();
        $this->getRepoReleaseInfo();

        // If nothing is found, do nothing
        if (empty($response->slug) || $response->slug != $this->slug) {
            return false;
        }

        // Add our plugin information
        $response->last_updated = $this->githubAPIResult->published_at;
        $response->slug = $this->slug;
        $response->plugin_name = $this->pluginData["Name"];
        $response->version = $this->githubAPIResult->tag_name;
        $response->author = $this->pluginData["AuthorName"];
        $response->homepage = $this->pluginData["PluginURI"];

        // Release download zip file
        if ($this->githubAPIResult->assets) {
            $asset = current($this->githubAPIResult->assets);
            $response->download_link = $asset->browser_download_url;
        }

        $parsedown = new \Parsedown();
        // Create tabs in the lightbox
        $response->sections = array(
            'description' => $this->pluginData["Description"],
            'changelog' => $parsedown->parse($this->githubAPIResult->body),
        );

        // Gets the required version of WP if available
        $matches = null;
        preg_match("/requires:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches);
        if (!empty($matches)) {
            if (is_array($matches)) {
                if (count($matches) > 1) {
                    $response->requires = $matches[1];
                }
            }
        }

        // Gets the tested version of WP if available
        $matches = null;
        preg_match("/tested:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches);
        if (!empty($matches)) {
            if (is_array($matches)) {
                if (count($matches) > 1) {
                    $response->tested = $matches[1];
                }
            }
        }

        return $response;
    }

    /**
     * Perform additional actions to successfully install our plugin
     *
     * @param $true
     * @param $hook_extra
     * @param $result
     *
     * @return mixed
     */
    public function postInstall($true, $hook_extra, $result)
    {
        // Get plugin information
        $this->initPluginData();

        // Remember if our plugin was previously activated
        $wasActivated = is_plugin_active($this->slug);

        // Since we are hosted in GitHub, our plugin folder would have a dirname of
        // reponame-tagname change it to our original one:
        /** @var \WP_Filesystem_Base $wp_filesystem */
        global $wp_filesystem;
        $pluginFolder = WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.dirname($this->slug);
        $wp_filesystem->move($result['destination'], $pluginFolder);
        $result['destination'] = $pluginFolder;

        // Re-activate plugin if needed
        if ($wasActivated) {
            activate_plugin($this->slug);
        }

        // code here
        return $result;
    }
}