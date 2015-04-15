<?php
namespace b3nl\SWRedis\Session;

use Predis\ClientInterface;
use Zend_Session_SaveHandler_Interface;

/**
 * Saves session with a redis service.
 * @author blange <github@b3nl.de>
 * @package b3nl\SWRedis
 * @subpackage Session
 * @version $id$
 */
class SaveHandler implements Zend_Session_SaveHandler_Interface
{
    /**
     * Caching of the lifetime.
     * @var int
     */
    protected $lifetime = -1;

    /**
     * The redis clients?
     * @var ClientInterface|void
     */
    protected $redisClient = null;

    /**
     * Construct.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->redisClient = $client;
    } // function

    /**
     * Close Session - free resources
     * @return void
     */
    public function close()
    {
        // do nothing, like in the dbtable adapter.
        return true;
    } // function

    /**
     * Destroy Session - remove data from resource for
     * given session id
     * @param string $id
     * @return void
     */
    public function destroy($id)
    {
        $this->redisClient->del($id);
    } // function

    /**
     * Garbage Collection - remove old session data older
     * than $maxlifetime (in seconds)
     * @param int $maxlifetime
     * @return void
     */
    public function gc($maxlifetime)
    {
        // do nothing, we work with a ttl.
    } // function

    /**
     * Returns the redis client.
     * @return ClientInterface
     */
    public function getRedisClient()
    {
        return $this->redisClient;
    } // function

    /**
     * Returns the session lifetime.
     * @return int
     */
    public function getSessionLifetime()
    {
        if ($this->lifetime === -1) {
            $this->lifetime = (int) ini_get('session.gc_maxlifetime');
        } // if

        return $this->lifetime;
    } // function

    /**
     * Open Session - retrieve resources
     * @param string $save_path
     * @param string $name
     * @return void
     */
    public function open($save_path, $name)
    {
        // do nothing, redis connect allready done.
    } // function

    /**
     * Read session data
     * @param string $id
     * @return mixed
     */
    public function read($id)
    {
        $client = $this->getRedisClient();
        $data = $client->get($id);

        $client->expire($id, $this->getSessionLifetime());

        return $data;
    } // function

    /**
     * Sets the lifetime for the session.
     * @param $lifetime
     * @return SaveHandler
     */
    public function setSessionLifetime($lifetime)
    {
        $this->lifetime = $lifetime;

        return $this;
    } // function

    /**
     * Write Session - commit data to resource
     * @param string $id
     * @param mixed $data
     * @return void
     */
    public function write($id, $data)
    {
        $client = $this->getRedisClient();

        $client->set($id, $data);
        $client->expire($id, $this->getSessionLifetime());
    } // function
}
