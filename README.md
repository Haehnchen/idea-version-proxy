espendIdeaVersionProxy
=================

Provides a simple url callback to get direct download url for [JetBrains Plugin Repository](https://plugins.jetbrains.com/)

## Install

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new espend\IdeaVersionProxyBundle\espendIdeaVersionProxyBundle(),
    );
}
```

### Routing

```
# app/routing.xml
espend_version_proxy:
    resource: "@espendIdeaVersionProxyBundle/Resources/config/routing.xml"
```

## Endpoints

```
    /version-proxy/{pluginId}/latest
    [GET] /version-proxy/7219/latest
    [GET] /version-proxy/7219/latest.json

    /version-proxy/{pluginId}/{version}.json
    [GET] /version-proxy/7219/0.12.120
    [GET] /version-proxy/7219/0.12.120.json
```