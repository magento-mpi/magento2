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
 * Adminhtml dashboard bottom tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Dashboard_Grids extends Mage_Adminhtml_Block_Widget_Tabs
{

    protected $_template = 'widget/tabshoriz.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid_tab');
        $this->setDestElementId('grid_tab_content');
    }

    /**
     * Prepare layout for dashboard bottom tabs
     *
     * To load block statically:
     *     1) content must be generated
     *     2) url should not be specified
     *     3) class should not be 'ajax'
     * To load with ajax:
     *     1) do not load content
     *     2) specify url (BE CAREFUL)
     *     3) specify class 'ajax'
     *
     * @return Mage_Adminhtml_Block_Dashboard_Grids
     */
    protected function _prepareLayout()
    {
        // load this active tab statically
        $this->addTab('ordered_products', array(
            'label'     => __('Bestsellers'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Dashboard_Tab_Products_Ordered')->toHtml(),
            'active'    => true
        ));

        // load other tabs with ajax
        $this->addTab('reviewed_products', array(
            'label'     => __('Most Viewed Products'),
            'url'       => $this->getUrl('*/*/productsViewed', array('_current'=>true)),
            'class'     => 'ajax'
        ));

        $this->addTab('new_customers', array(
            'label'     => __('New Customers'),
            'url'       => $this->getUrl('*/*/customersNewest', array('_current'=>true)),
            'class'     => 'ajax'
        ));

        $this->addTab('customers', array(
            'label'     => __('Customers'),
            'url'       => $this->getUrl('*/*/customersMost', array('_current'=>true)),
            'class'     => 'ajax'
        ));

        return parent::_prepareLayout();
    }
}
