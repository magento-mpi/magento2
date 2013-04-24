<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional html fot the Create Website button, which provides custom "onclick" functionality
 */
class Mage_Adminhtml_Block_System_Store_Store_Button_CreateWebsiteJs extends Mage_Backend_Block_Template
{
    /**
     * Path to custom template file
     *
     * @var string
     */
    protected $_template = 'system/store/store/button/create_website_js.phtml';

    /**
     * Check whether website creation is restricted
     *
     * @return bool
     */
    public function isCreateRestricted()
    {
        return $this->_getLimitation()->isCreateRestricted();
    }

    /**
     * Get message about reached limitation
     *
     * @return string
     */
    public function getCreateRestrictedMessage()
    {
        return $this->_getLimitation()->getCreateRestrictedMessage();
    }

    /**
     * Get limitation model
     *
     * @return Mage_Core_Model_Website_Limitation
     */
    protected function _getLimitation()
    {
        return Mage::getObjectManager()->get('Mage_Core_Model_Website_Limitation');
    }

    /**
     * Get url to create a new website
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/newWebsite');
    }
}
