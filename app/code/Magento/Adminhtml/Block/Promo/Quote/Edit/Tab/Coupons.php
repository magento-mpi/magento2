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
 * "Manage Coupons Codes" Tab
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Promo_Quote_Edit_Tab_Coupons
    extends Magento_Adminhtml_Block_Text_List
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Manage Coupon Codes');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Manage Coupon Codes');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return $this->_isEditing();
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return !$this->_isEditing();
    }

    /**
     * Check whether we edit existing rule or adding new one
     *
     * @return bool
     */
    protected function _isEditing()
    {
        $priceRule = $this->_coreRegistry->registry('current_promo_quote_rule');
        return !is_null($priceRule->getRuleId());
    }
}
