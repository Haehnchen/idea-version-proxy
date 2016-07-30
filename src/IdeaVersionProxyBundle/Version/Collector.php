<?php

namespace espend\IdeaVersionProxyBundle\Version;

/**
 * @author Daniel Espendiller <daniel@espendiller.net>
 */
class Collector
{
    /**
     * Base url for file storage of JetBrains repository
     */
    const PLUGIN_DOWNLOAD_BASE = 'https://plugins.jetbrains.com/files/';

    /**
     * @var \espend\IdeaVersionProxyBundle\Version\Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $pluginId
     * @return array<string, string>
     */
    private function collect($pluginId)
    {
        if(null === $contents = $this->request->request('plugin/updates?pluginId='. $pluginId .'&start=0&size=75')) {
            return [];
        }

        if(!($version = json_decode($contents, true)) || !isset($version['updates'])) {
            return [];
        }

        $versions = [];
        foreach($version['updates'] as $update) {
            if(!isset($update['updateVersion'], $update['file'])) {
                continue;
            }

            $versions[$update['updateVersion']] = $update['file'];
        }

        return $versions;
    }

    /**
     * @param string $pluginId
     * @param string $version
     * @return string|null
     */
    private function find($pluginId, $version)
    {
        $versions = $this->collect($pluginId);
        if(!isset($versions[$version])) {
            return null;
        }

        return $versions[$version];
    }

    /**
     * @param string $pluginId
     * @param string $version
     * @return null|string
     */
    public function resolve($pluginId, $version)
    {
        if(!$url = $this->find($pluginId, $version)) {
            return null;
        }

        return self::PLUGIN_DOWNLOAD_BASE . ltrim($url, '/');
    }

    /**
     * @param string $pluginId
     * @return null|string
     */
    public function latest($pluginId)
    {
        $versions = $this->collect($pluginId);
        if(count($versions) == 0) {
            return null;
        }
        
        uksort($versions, 'version_compare');

        return self::PLUGIN_DOWNLOAD_BASE . ltrim(end($versions), '/');
    }
}