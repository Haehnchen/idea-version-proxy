<?php

namespace espend\IdeaVersionProxyBundle\Controller;

use espend\IdeaVersionProxyBundle\Version\Collector;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Daniel Espendiller <daniel@espendiller.net>
 */
class DownloadController
{
    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var Collector
     */
    private $collector;

    public function __construct(Collector $collector)
    {
        $this->collector = $collector;
        $this->headers = [
            'Cache-Control' => 's-maxage=3600, public',
        ];
    }

    /**
     * @param string $pluginId
     * @param string $version
     * @return Response
     */
    public function downloadAction($pluginId, $version)
    {
        $url = $this->collector->resolve($pluginId, $version);
        if(!$url) {
            return new Response('Version not found', 404, $this->headers);
        }

        return new RedirectResponse($url, 302, $this->headers);
    }

    /**
     * @param string $pluginId
     * @return Response
     */
    public function latestAction($pluginId)
    {
        $url = $this->collector->latest($pluginId);
        if(!$url) {
            return new Response('Version not found', 404, $this->headers);
        }

        return new RedirectResponse($url, 302, $this->headers);
    }


    /**
     * @param string $pluginId
     * @param string $version
     * @return JsonResponse
     */
    public function downloadJsonAction($pluginId, $version)
    {
        $url = $this->collector->resolve($pluginId, $version);
        if(!$url) {
            return new JsonResponse(['message' => 'Version not found'], 404, $this->headers);
        }

        return new JsonResponse(['url' => $url], 200, $this->headers);
    }

    /**
     * @param string $pluginId
     * @return JsonResponse
     */
    public function latestJsonAction($pluginId)
    {
        $url = $this->collector->latest($pluginId);
        if(!$url) {
            return new JsonResponse(['message' => 'Version not found'], 404, $this->headers);
        }

        return new JsonResponse(['url' => $url], 200, $this->headers);
    }
}
