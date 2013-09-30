<?php
/**
 * Backend user session
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * @param Magento_Core_Model_Session_Context $context
     * @param array $data
     */
    public function __construct(Magento_Core_Model_Session_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->init('adminhtml');
    }
}
