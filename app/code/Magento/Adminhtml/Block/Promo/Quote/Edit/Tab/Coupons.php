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
namespace Magento\Adminhtml\Block\Promo\Quote\Edit\Tab;

class Coupons
    extends \Magento\Adminhtml\Block\Text\ListText
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Context $context,
        \Magento\Core\Model\Registry $registry,
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
