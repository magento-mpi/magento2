<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional buttons on customer edit form
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Customer_Edit_Buttons extends Magento_Adminhtml_Block_Customer_Edit
{
    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $registry, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * Add "Manage Shopping Cart" button on customer management page
     *
     * @return Magento_AdvancedCheckout_Block_Adminhtml_Customer_Edit_Buttons
     */
    public function addButtons()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        if (!$this->_authorization->isAllowed('Magento_AdvancedCheckout::view')
            && !$this->_authorization->isAllowed('Magento_AdvancedCheckout::update')
            || $this->_storeManager->getStore()->getWebsiteId() == $customer->getWebsiteId()
        ) {
            return $this;
        }
        $container = $this->getParentBlock();
        if ($container instanceof Magento_Backend_Block_Template && $container->getCustomerId()) {
            $url = Mage::getSingleton('Magento_Backend_Model_Url')->getUrl('*/checkout/index', array(
                'customer' => $container->getCustomerId()
            ));
            $container->addButton('manage_quote', array(
                'label' => __('Manage Shopping Cart'),
                'onclick' => "setLocation('" . $url . "')",
            ), 0);
        }
        return $this;
    }
}
