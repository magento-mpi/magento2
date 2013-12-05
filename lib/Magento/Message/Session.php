<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * Message session model
 */
class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * Message session namespace
     */
    const MESSAGE_NAMESPACE = 'message';

    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\Session\Config\ConfigInterface $sessionConfig
     * @param \Zend_Session_SaveHandler_Interface $saveHandler
     * @param \Magento\Session\ValidatorInterface $validator
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\Session\Config\ConfigInterface $sessionConfig,
        \Zend_Session_SaveHandler_Interface $saveHandler,
        \Magento\Session\ValidatorInterface $validator,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($context, $sidResolver, $sessionConfig, $saveHandler, $validator, $data);
        $this->start(self::MESSAGE_NAMESPACE, $sessionName);
    }
}
