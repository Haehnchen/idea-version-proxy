<?php

namespace espend\IdeaVersionProxyBundle\Version;

use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Daniel Espendiller <daniel@espendiller.net>
 */
class Collector
{

    /**
     * @var \espend\IdeaVersionProxyBundle\Version\Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $pluginId
     * @return array
     */
    private function collect($pluginId)
    {
        if(null === $contents = $this->request->request('/plugin/' . $pluginId . '?showAllUpdates=true')) {
            return [];
        }

        $crawler = new Crawler($contents);

        $versions = [];

        foreach($crawler->filter('table.version_table tr td:first-child a') as $child) {
            /** @var $child \DOMElement */

            $query = [];
            parse_str(parse_url($child->getAttribute('href'), PHP_URL_QUERY), $query);
            if(!isset($query['updateId'])) {
                continue;
            }

            $versions[$child->textContent] = '/plugin/download?updateId=' . $query['updateId'];
        }

        return $versions;
    }

    /**
     * @param $pluginId
     * @param $version
     * @return mixed|null
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
     * @param $pluginId
     * @param $version
     * @return null|string
     */
    public function resolve($pluginId, $version)
    {
        if(!$url = $this->find($pluginId, $version)) {
            return null;
        }

        return $this->request->requestLocation($url);
    }

    /**
     * @param $pluginId
     * @return null|string
     */
    public function latest($pluginId)
    {
        $versions = $this->collect($pluginId);
        if(count($versions) == 0) {
            return null;
        }
        
        uksort($versions, 'version_compare');

        return $this->request->requestLocation(end($versions));
    }
}