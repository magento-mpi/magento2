<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *  Add sales archiving to order's grid view massaction
 *  @deprecated
 */
class Magento_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction
    extends Magento_Adminhtml_Block_Widget_Grid_Massaction_Abstract
{
    /**
     * @var Magento_SalesArchive_Model_Config
     */
    protected $_configModel;

    /**
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_SalesArchive_Model_Config $configModel
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $backendData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_SalesArchive_Model_Config $configModel,
        array $data = array()
    ) {
        $this->_configModel = $configModel;
        parent::__construct($backendData, $coreData, $context, $data);
    }


    /**
     * Before rendering html operations
     *
     * @return Magento_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction
     */
    protected function _beforeToHtml()
    {
        $isActive = $this->_configModel->isArchiveActive();
        if ($isActive && $this->_authorization->isAllowed('Magento_SalesArchive::add')) {
            $this->addItem('add_order_to_archive', array(
                 'label'=> __('Move to Archive'),
                 'url'  => $this->getUrl('*/sales_archive/massAdd'),
            ));
        }
        return parent::_beforeToHtml();
    }
}
