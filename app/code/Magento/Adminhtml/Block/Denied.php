<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Denied extends Magento_Adminhtml_Block_Template
{
    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_authSession = $authSession;
        parent::__construct($coreData, $context, $data);
    }

    public function hasAvailableResources()
    {
        $user = $this->authSession->getUser();
        if ($user && $user->getHasAvailableResources()) {
            return true;
        }
        return false;
    }
}
