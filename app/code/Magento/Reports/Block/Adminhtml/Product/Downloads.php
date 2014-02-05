<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Product;

/**
 * Adminhtml product downloads report
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Downloads extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reports';
        $this->_controller = 'adminhtml_product_downloads';
        $this->_headerText = __('Downloads');
        parent::_construct();
        $this->_removeButton('add');
    }

    /**
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->setChild('store_switcher',
            $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher')
                ->setUseConfirm(false)
                ->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null)))
                ->setTemplate('Magento_Reports::store/switcher.phtml')
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        if (!$this->_storeManager->isSingleStoreMode()) {
            return $this->getChildHtml('store_switcher');
        }
        return '';
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getStoreSwitcherHtml() . parent::getGridHtml();
    }
}
