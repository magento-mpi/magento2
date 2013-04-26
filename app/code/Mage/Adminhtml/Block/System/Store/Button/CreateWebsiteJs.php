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
class Mage_Adminhtml_Block_System_Store_Button_CreateWebsiteJs extends Mage_Backend_Block_Template
{
    /**
     * Path to custom template file
     *
     * @var string
     */
    protected $_template = 'system/store/store/button/create_website_js.phtml';

    /**
     * Cached limitation model, which is used to fetch information about the website limitation
     *
     * @var Mage_Core_Model_Website_Limitation
     */
    protected $_limitationModel;

    /**
     * Html id of the button, managed by this block's Javascript
     */
    protected $_htmlId = null;

    /**
     * Html id of the button that is managed by this block's Javascript
     *
     * @param string $id
     */
    public function setHtmlId($id)
    {
        $this->_htmlId = $id;
    }

    /**
     * Get html id, to be used by Javascript
     *
     * @return string
     * @throws Mage_Adminhtml_Exception
     */
    public function getHtmlId()
    {
        if ($this->_htmlId === null) {
            throw new Mage_Adminhtml_Exception('The button\'s html id is not set');
        }
        return $this->_htmlId;
    }

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
        if (!$this->_limitationModel) {
            $this->_limitationModel = Mage::getObjectManager()->get('Mage_Core_Model_Website_Limitation');
        }
        return $this->_limitationModel;
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
