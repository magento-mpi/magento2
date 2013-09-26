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
    protected $_collectionFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Rma_Model_Config $rmaConfig
     * @param Magento_Rma_Model_Resource_Rma_Status_History_CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Rma_Model_Config $rmaConfig,
        Magento_Rma_Model_Resource_Rma_Status_History_CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_rmaConfig = $rmaConfig;
        $this->_collectionFactory = $collectionFactory;
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
        $this->_rmaConfig->init($this->_rmaConfig->getRootCommentEmail(), $this->getOrder()->getStore());
        return $this->_rmaConfig->isEnabled();
    }

    /**
     * Get config value - is Enabled RMA Email
     *
     * @return bool
     */
    public function canSendConfirmationEmail()
    {
        $this->_rmaConfig->init($this->_rmaConfig->getRootRmaEmail(), $this->getOrder()->getStore());
        return $this->_rmaConfig->isEnabled();
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

    /**
     * @return Magento_Rma_Model_Resource_Rma_Status_History_Collection
     */
    public function getComments()
    {
        /** @var $collection Magento_Rma_Model_Resource_Rma_Status_History_Collection */
        $collection = $this->_collectionFactory->create();
        return $collection->addFieldToFilter('rma_entity_id', $this->_coreRegistry->registry('current_rma')->getId());
    }
}
