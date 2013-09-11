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
 * Adminhtml catalog product sets main page toolbar
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Attribute\Set\Toolbar;

class Main extends \Magento\Adminhtml\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/attribute/set/toolbar/main.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('addButton', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Add New Set'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/add') . '\')',
            'class' => 'add',
        ));
        return parent::_prepareLayout();
    }

    public function getNewButtonHtml()
    {
        return $this->getChildHtml('addButton');
    }

    protected function _getHeader()
    {
        return __('Product Templates');
    }

    protected function _toHtml()
    {
        \Mage::dispatchEvent('adminhtml_catalog_product_attribute_set_toolbar_main_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}
