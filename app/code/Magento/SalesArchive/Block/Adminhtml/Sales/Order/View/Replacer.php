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
 * Sales archive order view replacer for archive
 *
 */
class Magento_SalesArchive_Block_Adminhtml_Sales_Order_View_Replacer
    extends Magento_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * @var Magento_SalesArchive_Model_Config
     */
    protected $_archiveConfig;

    /**
     * @param Magento_SalesArchive_Model_Config $archiveConfig
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_SalesArchive_Model_Config $archiveConfig,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_archiveConfig = $archiveConfig;
        parent::__construct($coreData, $context, $registry, $data);
    }

    protected function _prepareLayout()
    {
        if ($this->getOrder()->getIsArchived()) {
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'magento_order_shipments',
                'Magento_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Shipments'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'magento_order_invoices',
                'Magento_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Invoices'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'magento_order_creditmemos',
                'Magento_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Creditmemos'
            );

            $restoreUrl = $this->getUrl(
                '*/sales_archive/remove',
                array('order_id' => $this->getOrder()->getId())
            );
            if ($this->_authorization->isAllowed('Magento_SalesArchive::remove')) {
                $this->getLayout()->getBlock('sales_order_edit')->addButton('restore', array(
                    'label' => __('Move to Order Managment'),
                    'onclick' => 'setLocation(\'' . $restoreUrl . '\')',
                    'class' => 'cancel'
                ));
            }
        } elseif ($this->getOrder()->getIsMoveable() !== false) {
            $isActive = $this->_archiveConfig->isArchiveActive();
            if ($isActive) {
                $archiveUrl = $this->getUrl(
                    '*/sales_archive/add',
                    array('order_id' => $this->getOrder()->getId())
                );
                if ($this->_authorization->isAllowed('Magento_SalesArchive::add')) {
                    $this->getLayout()->getBlock('sales_order_edit')->addButton('restore', array(
                        'label' => __('Move to Archive'),
                        'onclick' => 'setLocation(\'' . $archiveUrl . '\')',
                    ));
                }
            }
        }

        return $this;
    }

    protected function _toHtml()
    {
        return '';
    }
}
