<?php
    namespace b3nl\SWRedis\DI\Bridge;

    use b3nl\SWRedis\Session\SaveHandler,
        Predis\Client as PredisClient,
        Shopware\Components\DependencyInjection\Bridge\Session as SessionBase,
        Shopware\Components\DependencyInjection\Container;

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
            $sessionOptions = Shopware()->getOption('session', array());

            if (@$sessionOptions['save_handler'] === 'redis') {
                $redisOptions = array_merge(
                    [
                        'exceptions' => true,
                        'prefix' => 'session:'
                    ],
                    isset($sessionOptions['redis_options']) && is_array($sessionOptions['redis_options'])
                        ? $sessionOptions['redis_options']
                        : []
                );

                $redisOptions['prefix'] = @$redisOptions['prefix'] ?: 'session:';

                $client = new PredisClient($redisOptions);

                \Enlight_Components_Session::setSaveHandler(new SaveHandler($client));

                unset($sessionOptions['save_handler']);
            } // if

            return parent::factory($container);
        } // function
    }