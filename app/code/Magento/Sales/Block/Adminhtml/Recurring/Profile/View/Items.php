<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml recurring profile items grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Recurring\Profile\View;

class Items extends \Magento\Adminhtml\Block\Sales\Items\AbstractItems
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($coreData, $context, $registry, $data);
    }

    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Core\Exception(__('Invalid parent block for this block'));
        }
        parent::_beforeToHtml();
    }

    /**
     * Return current recurring profile
     *
     * @return \Magento\Sales\Model\Recurring\Profile
     */
    public function _getRecurringProfile()
    {
        return $this->_coreRegistry->registry('current_recurring_profile');
    }

    /**
     * Retrieve recurring profile item
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getItem()
    {
        return $this->_getRecurringProfile()->getItem();
    }

    /**
     * Retrieve formatted price
     *
     * @param   decimal $value
     * @return  string
     */
    public function formatPrice($value)
    {
        $store = $this->_storeManager->getStore($this->_getRecurringProfile()->getStore());
        return $store->formatPrice($value);
    }
}

