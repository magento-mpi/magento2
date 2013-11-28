<?php
/**
 * Backend user session
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Zend\Session\Config\ConfigInterface $sessionConfig
     * @param \Zend_Session_SaveHandler_Interface $saveHandler
     * @param \Magento\Session\ValidatorInterface $validator
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Zend\Session\Config\ConfigInterface $sessionConfig,
        \Zend_Session_SaveHandler_Interface $saveHandler,
        \Magento\Session\ValidatorInterface $validator,
        array $data = array()
    ) {
        parent::__construct($context, $sidResolver, $sessionConfig, $saveHandler, $validator, $data);
        $this->start('adminhtml');
    }
}
