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
 * Magento session save handler factory
 */
class SaveHandlerFactory
{
    /**
     * Php native session handler
     */
    const PHP_NATIVE_HANDLER = 'Magento\Session\SaveHandler\Native';

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $handlers = array();

    /**
     * @param \Magento\ObjectManager $objectManger
     * @param array $handlers
     */
    public function __construct(\Magento\ObjectManager $objectManger, array $handlers = array())
    {
        $this->objectManager = $objectManger;
        if (!empty($handlers)) {
            $this->handlers = array_merge($handlers, $this->handlers);
        }
    }

    /**
     * Create session save handler
     *
     * @param string $saveMethod
     * @param array $params
     * @return \SessionHandler
     * @throws \LogicException
     */
    public function create($saveMethod, $params = array())
    {
        $sessionHandler = self::PHP_NATIVE_HANDLER;
        if (isset($this->handlers[$saveMethod])) {
            $sessionHandler = $this->handlers[$saveMethod];
        }

        $model = $this->objectManager->create($sessionHandler, $params);
        if (!$model instanceof \SessionHandler) {
            throw new \LogicException($sessionHandler . ' doesn\'t implement \SessionHandler');
        }

        return $model;
    }
}
