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
 *
 */
class Magento_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Button extends Magento_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * @var Magento_SalesArchive_Model_Resource_Order_Collection
     */
    protected $_orderCollection;

    /**
     * @param Magento_SalesArchive_Model_Resource_Order_Collection $orderCollection
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_SalesArchive_Model_Resource_Order_Collection $orderCollection,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_orderCollection = $orderCollection;
        parent::__construct($coreData, $context, $registry, $data);
    }

    protected function _prepareLayout()
    {
        $ordersCount = $this->_orderCollection->getSize();
        $parent = $this->getLayout()->getBlock('sales_order.grid.container');
        if ($parent && $ordersCount) {
            $url = $this->getUrl('*/sales_archive/orders');
            $parent->addButton('go_to_archive',  array(
                'label'     => __('Go to Archive (%1 orders)', $ordersCount),
                'onclick'   => 'setLocation(\'' . $url . '\')',
                'class'     => 'go'
            ));
        }
        return $this;
    }

    protected function _toHtml()
    {
        return '';
    }
}
