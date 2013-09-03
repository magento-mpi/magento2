<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Block_Adminhtml_Catalog_Product_Edit_Tab_Giftcard
 extends Magento_Adminhtml_Block_Widget
 implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $coreStoreConfig, $data);
        $this->_storeManager = $storeManager;
    }

    protected $_template = 'catalog/product/edit/tab/giftcard.phtml';

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Gift Card Information');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Gift Card Information');
    }

    /**
     * Check if tab can be displayed
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Return true when current product is new
     *
     * @return bool
     */
    public function isNew()
    {
        if (Mage::registry('product')->getId()) {
            return false;
        }
        return true;
    }

    /**
     * Return field name prefix
     *
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'product';
    }

    /**
     * Get current product value, when no value available - return config value
     *
     * @param string $field
     * @return string
     */
    public function getFieldValue($field)
    {
        if (!$this->isNew()) {
            return Mage::registry('product')->getDataUsingMethod($field);
        }

        return $this->_coreStoreConfig->getConfig(Magento_GiftCard_Model_Giftcard::XML_PATH . $field);
    }

    /**
     * Return gift card types
     *
     * @return array
     */
    public function getCardTypes()
    {
        return array(
            Magento_GiftCard_Model_Giftcard::TYPE_VIRTUAL  => __('Virtual'),
            Magento_GiftCard_Model_Giftcard::TYPE_PHYSICAL => __('Physical'),
            Magento_GiftCard_Model_Giftcard::TYPE_COMBINED => __('Combined'),
        );
    }

    /**
     * Return email template select options
     *
     * @return array
     */
    public function getEmailTemplates()
    {
        $result = array();
        $template = Mage::getModel('Magento_Backend_Model_Config_Source_Email_Template');
        $template->setPath(Magento_GiftCard_Model_Giftcard::XML_PATH_EMAIL_TEMPLATE);
        foreach ($template->toOptionArray() as $one) {
            $result[$one['value']] = $this->escapeHtml($one['label']);
        }
        return $result;
    }

    public function getConfigValue($field)
    {
        return $this->_coreStoreConfig->getConfig(Magento_GiftCard_Model_Giftcard::XML_PATH . $field);
    }

    /**
     * Check block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return Mage::registry('product')->getGiftCardReadonly();
    }

    /**
     * Return 'value-scope' attribute with specified value if is not single store mode
     *
     * @param string $text
     * @return string
     */
    public function getScopeValue($text)
    {
        if ($this->_storeManager->isSingleStoreMode()) {
            return '';
        }
        return 'value-scope="' . __($text) . '"';
    }

    /**
     * Get parent tab code
     *
     * @return string
     */
    public function getParentTab()
    {
        return 'product-details';
    }
}
