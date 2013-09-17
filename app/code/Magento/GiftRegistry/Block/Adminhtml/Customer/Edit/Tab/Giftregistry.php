<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Customer_Edit_Tab_Giftregistry
    extends Magento_Backend_Block_Template
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * Gift registry data
     *
     * @var Magento_GiftRegistry_Helper_Data
     */
    protected $_giftRegistryData = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_GiftRegistry_Helper_Data $giftRegistryData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_GiftRegistry_Helper_Data $giftRegistryData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Set identifier and title
     */
    protected  function _construct()
    {
        parent::_construct();
        $this->setId('gifregustry');
        $this->setTitle(__('Gift Registry'));
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getTitle();
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTitle();
    }

    /**
     * Check whether tab can be showed
     *
     * @return bool
     */
    public function canShowTab()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        return $customer->getId()
           && $this->_giftRegistryData->isEnabled()
           && $this->_authorization->isAllowed('Magento_GiftRegistry::customer_magento_giftregistry');
    }

    /**
     * Check whether tab should be hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Precessor tab ID getter
     *
     * @return string
     */
    public function getAfter()
    {
        return 'reviews';
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Tab URL getter
     *
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/giftregistry_customer/grid', array('_current' => true));
    }
}
