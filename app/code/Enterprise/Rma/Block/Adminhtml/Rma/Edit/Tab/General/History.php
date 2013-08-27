<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Comments History Block at RMA page
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_History
    extends Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Abstract
{
    /**
     * Prepare child blocks
     *
     * @return Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_History
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('rma-history-block').parentNode, '".$this->getSubmitUrl()."')";
        $button = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => __('Submit Comment'),
                'class'   => 'save',
                'onclick' => $onclick
            ));
        $this->setChild('submit_button', $button);

        return parent::_prepareLayout();
    }

    /**
     * Get config value - is Enabled RMA Comments Email
     *
     * @return bool
     */
    public function canSendCommentEmail()
    {
        /** @var $configRmaEmail Enterprise_Rma_Model_Config */
        $configRmaEmail = Mage::getSingleton('Enterprise_Rma_Model_Config');
        $configRmaEmail->init($configRmaEmail->getRootCommentEmail(), $this->getOrder()->getStore());
        return $configRmaEmail->isEnabled();
    }

    /**
     * Get config value - is Enabled RMA Email
     *
     * @return bool
     */
    public function canSendConfirmationEmail()
    {
        /** @var $configRmaEmail Enterprise_Rma_Model_Config */
        $configRmaEmail = Mage::getSingleton('Enterprise_Rma_Model_Config');
        $configRmaEmail->init($configRmaEmail->getRootRmaEmail(), $this->getOrder()->getStore());
        return $configRmaEmail->isEnabled();
    }

    /**
     * Get URL to add comment action
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', array('id'=>$this->getRmaData('entity_id')));
    }

    public function getComments() {
        return Mage::getResourceModel('Enterprise_Rma_Model_Resource_Rma_Status_History_Collection')
            ->addFieldToFilter('rma_entity_id', Mage::registry('current_rma')->getId());
    }

}
