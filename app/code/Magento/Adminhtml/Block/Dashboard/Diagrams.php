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
 * Adminhtml dashboard diagram tabs
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Dashboard_Diagrams extends Magento_Adminhtml_Block_Widget_Tabs
{

    protected $_template = 'widget/tabshoriz.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('diagram_tab');
        $this->setDestElementId('diagram_tab_content');
    }

    protected function _prepareLayout()
    {
        $this->addTab('orders', array(
            'label'     => __('Orders'),
            'content'   => $this->getLayout()->createBlock('Magento_Adminhtml_Block_Dashboard_Tab_Orders')->toHtml(),
            'active'    => true
        ));

        $this->addTab('amounts', array(
            'label'     => __('Amounts'),
            'content'   => $this->getLayout()->createBlock('Magento_Adminhtml_Block_Dashboard_Tab_Amounts')->toHtml(),
        ));
        return parent::_prepareLayout();
    }
}
