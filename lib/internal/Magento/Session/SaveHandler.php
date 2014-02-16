<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    \Magento\Stdlib
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Session;

/**
 * Magento session save handler
 */
class SaveHandler implements SaveHandlerInterface
{
    /**
     * @var \SessionHandler
     */
    protected $saveHandlerAdapter;

    /**
     * @param SaveHandlerFactory $saveHandlerFactory
     * @param string $saveMethod
     * @param string $default
     */
    public function __construct(SaveHandlerFactory $saveHandlerFactory, $saveMethod, $default = self::DEFAULT_HANDLER)
    {
        try {
            $adapter = $saveHandlerFactory->create($saveMethod);
        } catch (SaveHandlerException $e) {
            $adapter = $saveHandlerFactory->create($default);
        }
        $this->saveHandlerAdapter = $adapter;
    }

    /**
     * Open Session - retrieve resources
     *
     * @param string $savePath
     * @param string $name
     * @return bool
     */
    public function open($savePath, $name)
    {
        return $this->saveHandlerAdapter->open($savePath, $name);
    }

    /**
     * Close Session - free resources
     */
    public function close()
    {
        return $this->saveHandlerAdapter->close();
    }

    /**
     * Read session data
     *
     * @param string $sessionId
     * @return string
     */
    public function read($sessionId)
    {
        return $this->saveHandlerAdapter->read($sessionId);
    }

    /**
     * Write Session - commit data to resource
     *
     * @param string $sessionId
     * @param mixed $data
     * @return bool
     */
    public function write($sessionId, $data)
    {
        return $this->saveHandlerAdapter->write($sessionId, $data);
    }

    /**
     * Destroy Session - remove data from resource for given session id
     *
     * @param string $sessionId
     * @return bool
     */
    public function destroy($sessionId)
    {
        return $this->saveHandlerAdapter->destroy($sessionId);
    }

    /**
     * Garbage Collection - remove old session data older
     * than $maxLifetime (in seconds)
     *
     * @param int $maxLifetime
     * @return bool
     */
    public function gc($maxLifetime)
    {
        return $this->saveHandlerAdapter->gc($maxLifetime);
    }
}
