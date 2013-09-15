<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml review main block
 */

namespace Magento\Adminhtml\Block\Review;

class Main extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        $this->_addButtonLabel = __('Add New Review');
        parent::_construct();

        $this->_controller = 'review';

        // lookup customer, if id is specified
        $customerId = $this->getRequest()->getParam('customerId', false);
        $customerName = '';
        if ($customerId) {
            $customer = \Mage::getModel('Magento\Customer\Model\Customer')->load($customerId);
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $customerName = $this->escapeHtml($customerName);
        }
        $productId = $this->getRequest()->getParam('productId', false);
        $productName = null;
        if ($productId) {
            $product = \Mage::getModel('Magento\Catalog\Model\Product')->load($productId);
            $productName =  $this->escapeHtml($product->getName());
        }

        if ($this->_coreRegistry->registry('usePendingFilter') === true) {
            if ($customerName) {
                $this->_headerText = __('Pending Reviews of Customer `%1`', $customerName);
            } else {
                $this->_headerText = __('Pending Reviews');
            }
            $this->_removeButton('add');
        } else {
            if ($customerName) {
                $this->_headerText = __('All Reviews of Customer `%1`', $customerName);
            } elseif ($productName) {
                $this->_headerText = __('All Reviews of Product `%1`', $productName);
            } else {
                $this->_headerText = __('All Reviews');
            }
        }
    }
}
