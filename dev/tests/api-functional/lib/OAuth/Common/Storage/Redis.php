<?php
namespace OAuth\Common\Storage;

use OAuth\Common\Storage\Exception\StorageException;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Token\TokenInterface;
use Predis\Client as Predis;

/*
 * Stores a token in a Redis server. Requires the Predis library available at https://github.com/nrk/predis
 */
class Redis implements TokenStorageInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var object|\Redis
     */
    protected $redis;

    /**
     * @var object|TokenInterface
     */
    protected $cachedTokens;

    /**
     * @param Predis $redis An instantiated and connected redis client
     * @param string $key The key to store the token under in redis.
     */
    public function __construct(Predis $redis, $key)
    {
        $this->redis = $redis;
        $this->key = $key;
        $this->cachedTokens = [];
    }

    /**
     * @return \OAuth\Common\Token\TokenInterface
     * @throws TokenNotFoundException
     */
    public function retrieveAccessToken($service)
    {
        if (!$this->hasAccessToken($service)) {
            throw new TokenNotFoundException('Token not found in redis');
        }

        if (isset($this->cachedTokens[$service])) {
            return $this->cachedTokens[$service];
        }

        $val = $this->hget($this->key, $service);

        return $this->cachedTokens[$service] = unserialize($val);
    }

    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     * @throws StorageException
     */
    public function storeAccessToken($service, TokenInterface $token)
    {
        // (over)write the token
        $this->redis->hset($this->key, $service, serialize($token));
        $this->cachedTokens[$service] = $token;

        // allow chaining
        return $this;
    }

    /**
     * @return bool
     */
    public function hasAccessToken($service)
    {
        if (isset($this->cachedTokens[$service]) && $this->cachedTokens[$service] instanceof TokenInterface) {
            return true;
        }

        return $this->redis->hexists($this->key, $service);
    }

    /**
     * Delete the users token. Aka, log out.
     */
    public function clearToken()
    {
        // memory
        $this->cachedTokens = [];

        // redis
        $keys = $this->redis->hkeys($this->key);
        $me = $this;
        // 5.3 compat

        // pipeline for performance
        $this->redis->pipeline(
            function ($pipe) use ($keys, $me) {

                foreach ($keys as $k) {
                    $pipe->hdel($me->getKey(), $k);
                }
            }
        );

        // allow chaining
        return $this;
    }

    /**
     * @return Predis $redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
    }
}
