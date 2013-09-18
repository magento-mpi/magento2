<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Comments History Block at RMA page
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_History
    extends Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Abstract
{
    /**
     * @var Magento_Rma_Model_Config
     */
    protected $_rmaConfig;

    /**
     * @var Magento_Rma_Model_Resource_Rma_Status_History_CollectionFactory
     */
    protected $_statusCollFactory;

    /**
     * @param Magento_Rma_Model_Resource_Rma_Status_History_CollectionFactory $statusCollFactory
     * @param Magento_Rma_Model_Config $rmaConfig
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Rma_Model_Resource_Rma_Status_History_CollectionFactory $statusCollFactory,
        Magento_Rma_Model_Config $rmaConfig,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_statusCollFactory = $statusCollFactory;
        $this->_rmaConfig = $rmaConfig;
        parent::__construct($coreData, $context, $registry, $data);
    }

    /**
     * Prepare child blocks
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_History
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
        /** @var $configRmaEmail Magento_Rma_Model_Config */
        $configRmaEmail = $this->_rmaConfig;
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
        /** @var $configRmaEmail Magento_Rma_Model_Config */
        $configRmaEmail = $this->_rmaConfig;
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
        return $this->_statusCollFactory
            ->create()
            ->addFieldToFilter('rma_entity_id', $this->_coreRegistry->registry('current_rma')->getId());
    }

}
