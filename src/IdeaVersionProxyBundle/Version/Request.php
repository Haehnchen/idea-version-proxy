<?php

namespace espend\IdeaVersionProxyBundle\Version;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Cache\CacheItemPoolInterface;

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
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var int
     */
    private $lifetime = 3600;

    public function __construct(
        ClientInterface $client,
        CacheItemPoolInterface $cache,
        $baseUrl = 'http://plugins.jetbrains.com'
    )
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->cache = $cache;
    }

    /**
     * Content
     *
     * @param $url
     * @return mixed|null|string
     */
    public function request($url)
    {
        $hash = 'request-version-proxy-' . md5($url);
        $cache = $this->cache->getItem($hash);

        if($cache->isHit()) {
            return $cache->get();
        }

        $content = null;
        try {
            $content = (string)$this->client
                ->request('GET', $this->baseUrl . '/' . ltrim($url, '/'))
                ->getBody();
        } catch (RequestException $e) {
        }

        $this->cache->save(
            $cache->set($content)->expiresAfter($this->lifetime)
        );
        
        return $content;
    }
}