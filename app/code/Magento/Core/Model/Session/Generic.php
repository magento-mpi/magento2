<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Session;

class Generic extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param \Zend_Session_SaveHandler_Interface $saveHandler
     * @param string $sessionNamespace
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        \Zend_Session_SaveHandler_Interface $saveHandler,
        $sessionNamespace,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($context, $saveHandler, $data);
        $this->init($sessionNamespace, $sessionName);
    }
}
