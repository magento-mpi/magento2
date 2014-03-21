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
 * Product alerts tab
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab;

class Alerts extends \Magento\Backend\Block\Widget\Tab
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/tab/alert.phtml';

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $accordion = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Accordion')
            ->setId('productAlerts');
        /* @var $accordion \Magento\Backend\Block\Widget\Accordion */

        $alertPriceAllow = $this->_storeConfig->getValue('catalog/productalert/allow_price', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
        $alertStockAllow = $this->_storeConfig->getValue('catalog/productalert/allow_stock', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);

        if ($alertPriceAllow) {
            $accordion->addItem('price', array(
                'title'     => __('We saved the price alert subscription.'),
                'content'   => $this->getLayout()
                    ->createBlock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts\Price')
                    ->toHtml() . '<br />',
                'open'      => true
            ));
        }
        if ($alertStockAllow) {
            $accordion->addItem('stock', array(
                'title'     => __('We saved the stock notification.'),
                'content'   => $this->getLayout()
                    ->createBlock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts\Stock'),
                'open'      => true
            ));
        }

        $this->setChild('accordion', $accordion);

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }

    /**
     * Tab is hidden
     *
     * @return bool
     */
    public function canShowTab()
    {
        $alertPriceAllow = $this->_storeConfig->getValue('catalog/productalert/allow_price', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
        $alertStockAllow = $this->_storeConfig->getValue('catalog/productalert/allow_stock', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
        return ($alertPriceAllow || $alertStockAllow) && parent::canShowTab();
    }
}
