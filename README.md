# SWRedis
Redis Implementation for Shopware.

To enable redis session in your shopware 5, just follow this easy steps.

1. Create/update your engine/Shopware/Components/DependencyInjection/services_local.xml with `<service id="session_factory" class="b3nl\SWRedis\DI\Bridge\Session" />`
2. Overwrite the the session savehandler in your shopware config: `'session' => ['save_handler' => 'redis']`
3. And define a redis config with the key `sessionredis` if you like. Please use the config for a [predis/predis](https://github.com/nrk/predis) client. 
