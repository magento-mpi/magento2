<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product alerts tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts extends Mage_Adminhtml_Block_Template
{
    protected $_template = 'catalog/product/tab/alert.phtml';

    protected function _prepareLayout()
    {
        $accordion = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Accordion')
            ->setId('productAlerts');
        /* @var $accordion Mage_Adminhtml_Block_Widget_Accordion */

        $alertPriceAllow = Mage::getStoreConfig('catalog/productalert/allow_price');
        $alertStockAllow = Mage::getStoreConfig('catalog/productalert/allow_stock');

        if ($alertPriceAllow) {
            $accordion->addItem('price', array(
                'title'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('We saved the price alert subscription.'),
                'content'   => $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts_Price')
                    ->toHtml() . '<br />',
                'open'      => true
            ));
        }
        if ($alertStockAllow) {
            $accordion->addItem('stock', array(
                'title'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('We saved the stock notification.'),
                'content'   => $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts_Stock'),
                'open'      => true
            ));
        }

        $this->setChild('accordion', $accordion);

        return parent::_prepareLayout();
    }

    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }
}
