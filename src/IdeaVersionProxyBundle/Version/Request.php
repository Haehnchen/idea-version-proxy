<?php

namespace espend\IdeaVersionProxyBundle\Version;

use Doctrine\Common\Cache\Cache;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * @author Daniel Espendiller <daniel@espendiller.net>
 */
class Request
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var int
     */
    private $lifetime = 3600;

    public function __construct(
        ClientInterface $client,
        Cache $cache,
        $baseUrl = 'http://plugins.jetbrains.com'
    )
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->cache = $cache;
    }

    public function request($url)
    {
        $hash = md5($url);
        if(false !== $content = $this->cache->fetch($hash)) {
            return $content;
        }

        $content = null;
        try {
            $content = (string)$this->client
                ->request('GET', $this->baseUrl . '/' . ltrim($url, '/'))
                ->getBody();
        } catch (RequestException $e) {
        }

        $this->cache->save($hash, $content, $this->lifetime);
        
        return $content;
    }

    public function requestLocation($url)
    {
        $hash = md5($url);
        if(false !== $content = $this->cache->fetch($hash)) {
            return $content;
        }

        $content = null;
        try {
            $content = $this->client->request('GET', $this->baseUrl . '/' . ltrim($url, '/'))
                ->getHeaderLine('Location');
        } catch (RequestException $e) {
        }

        $this->cache->save($hash, $content, $this->lifetime);

        return $content;
    }
}