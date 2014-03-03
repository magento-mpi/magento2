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
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Toolbar;

class Main extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/attribute/set/toolbar/main.phtml';

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild('addButton', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Add New Set'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('catalog/*/add') . '\')',
            'class' => 'add',
        ));
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getNewButtonHtml()
    {
        return $this->getChildHtml('addButton');
    }

    /**
     * @return string
     */
    protected function _getHeader()
    {
        return __('Product Templates');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->_eventManager->dispatch('adminhtml_catalog_product_attribute_set_toolbar_main_html_before', array(
            'block' => $this,
        ));
        return parent::_toHtml();
    }
}
