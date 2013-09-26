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
    extends Magento_Backend_Block_Widget_Grid_Massaction_Abstract
{
    /**
     * @var Magento_SalesArchive_Model_Config
     */
    protected $_archiveConfig;

    /**
     * @param Magento_SalesArchive_Model_Config $archiveConfig
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_SalesArchive_Model_Config $archiveConfig,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_archiveConfig = $archiveConfig;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Before rendering html operations
     *
     * @return Magento_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction
     */
    protected function _beforeToHtml()
    {
        $isActive = $this->_archiveConfig->isArchiveActive();
        if ($isActive && $this->_authorization->isAllowed('Magento_SalesArchive::add')) {
            $this->addItem('add_order_to_archive', array(
                 'label'=> __('Move to Archive'),
                 'url'  => $this->getUrl('*/sales_archive/massAdd'),
            ));
        }
        return parent::_beforeToHtml();
    }
}
