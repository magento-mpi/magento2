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
 * Products mass update inventory tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Action\Attribute\Tab;

class Inventory
    extends \Magento\Adminhtml\Block\Widget
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\CatalogInventory\Model\Source\Backorders
     */
    protected $_backorders;

    /**
     * @param \Magento\CatalogInventory\Model\Source\Backorders $backorders
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\CatalogInventory\Model\Source\Backorders $backorders,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_backorders = $backorders;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve Backorders Options
     *
     * @return array
     */
    public function getBackordersOption()
    {
        return $this->_backorders->toOptionArray();
    }

    /**
     * Retrieve field suffix
     *
     * @return string
     */
    public function getFieldSuffix()
    {
        return 'inventory';
    }

    /**
     * Retrieve current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        $storeId = $this->getRequest()->getParam('store');
        return intval($storeId);
    }

    /**
     * Get default config value
     *
     * @param string $field
     * @return mixed
     */
    public function getDefaultConfigValue($field)
    {
        return $this->_storeConfig->getConfig(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_ITEM . $field, $this->getStoreId());
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return __('Advanced Inventory');
    }

    public function getTabTitle()
    {
        return __('Advanced Inventory');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
