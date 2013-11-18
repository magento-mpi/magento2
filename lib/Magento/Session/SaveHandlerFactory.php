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
        // @todo START: DELETE IT AFTER CONFIG IMPLEMENTATION

        switch($saveMethod) {
            case 'db':
                ini_set('session.save_handler', 'user');
                break;
            case 'memcache':
                ini_set('session.save_handler', 'memcache');
                break;
            case 'memcached':
                ini_set('session.save_handler', 'memcached');
                break;
            case 'files':
                ini_set('session.save_handler', 'files');
                break;
            case 'eaccelerator':
                ini_set('session.save_handler', 'eaccelerator');
                break;
            default:
                session_module_name($saveMethod);
                break;
        }
        // @todo FINISH: DELETE IT AFTER CONFIG IMPLEMENTATION

        $sessionHandler = '\SessionHandler';
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
