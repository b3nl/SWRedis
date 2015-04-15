<?php
namespace b3nl\SWRedis\DI\Bridge;

use b3nl\SWRedis\Session\SaveHandler;
use Predis\Client as PredisClient;
use Shopware\Components\DependencyInjection\Bridge\Session as SessionBase;
use Shopware\Components\DependencyInjection\Container;

/**
 * Injects the session to redis.
 * @author blange <github@b3nl.de>
 * @package b3nl\SWRedis
 * @subpackage DI\Bridge
 * @version $id$
 */
class Session extends SessionBase
{
    /**
     * Starts the redis connection.
     * @param Container $container
     * @return \Enlight_Components_Session_Namespace
     */
    public function factory(Container $container)
    {
        $sessionOptions = Shopware()->getOption('session', []);

        if (@$sessionOptions['save_handler'] === 'redis') {
            $redisOptions = array_merge(
                [
                    'exceptions' => true,
                    'prefix' => 'session:'
                ],
                Shopware()->getOption('sessionredis', [])
            );

            $client = new PredisClient($redisOptions);

            \Enlight_Components_Session::setSaveHandler(new SaveHandler($client));
        } // if

        return parent::factory($container);
    } // function
}
