<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml abandoned shopping cart report page content block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Adminhtml\Shopcart;

class Abandoned extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reports';
        $this->_controller = 'adminhtml_shopcart_abandoned';
        $this->_headerText = __('Abandoned carts');
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
        if ($this->_storeManager->isSingleStoreMode()) {
            return '';
        }
        return $this->getChildHtml('store_switcher');
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getStoreSwitcherHtml() . parent::getGridHtml();
    }

}
