<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to render form
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Block_Form extends Magento_Core_Block_Template
{
    /**
     * Invitation Config
     * 
     * @var Magento_Invitation_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Invitation_Model_Config $config
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData, 
        Magento_Core_Block_Template_Context $context,
        Magento_Invitation_Model_Config $config,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_config = $config;
    }

    /**
     * Returns maximal number of invitations to send in one try
     *
     * @return int
     */
    public function getMaxInvitationsPerSend()
    {
        return $this->_config->getMaxInvitationsPerSend();
    }

    /**
     * Returns whether custom invitation message allowed
     *
     * @return bool
     */
    public function isInvitationMessageAllowed()
    {
        return $this->_config->isInvitationMessageAllowed();
    }
}
