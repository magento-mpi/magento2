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
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab;

class Alerts extends \Magento\Adminhtml\Block\Template
{
    protected $_template = 'catalog/product/tab/alert.phtml';

    protected function _prepareLayout()
    {
        $accordion = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Accordion')
            ->setId('productAlerts');
        /* @var $accordion \Magento\Adminhtml\Block\Widget\Accordion */

        $alertPriceAllow = $this->_storeConfig->getConfig('catalog/productalert/allow_price');
        $alertStockAllow = $this->_storeConfig->getConfig('catalog/productalert/allow_stock');

        if ($alertPriceAllow) {
            $accordion->addItem('price', array(
                'title'     => __('We saved the price alert subscription.'),
                'content'   => $this->getLayout()
                    ->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Alerts\Price')
                    ->toHtml() . '<br />',
                'open'      => true
            ));
        }
        if ($alertStockAllow) {
            $accordion->addItem('stock', array(
                'title'     => __('We saved the stock notification.'),
                'content'   => $this->getLayout()
                    ->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Alerts\Stock'),
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
