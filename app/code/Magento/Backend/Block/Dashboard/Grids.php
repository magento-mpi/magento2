<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard bottom tabs
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\Dashboard;

class Grids extends \Magento\Backend\Block\Widget\Tabs
{

    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';

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
     * @return \Magento\Backend\Block\Dashboard\Grids
     */
    protected function _prepareLayout()
    {
        // load this active tab statically
        $this->addTab('ordered_products', array(
            'label'     => __('Bestsellers'),
            'content'   => $this->getLayout()
                ->createBlock('Magento\Backend\Block\Dashboard\Tab\Products\Ordered')->toHtml(),
            'active'    => true
        ));

        // load other tabs with ajax
        $this->addTab('reviewed_products', array(
            'label'     => __('Most Viewed Products'),
            'url'       => $this->getUrl('adminhtml/*/productsViewed', array('_current'=>true)),
            'class'     => 'ajax'
        ));

        $this->addTab('new_customers', array(
            'label'     => __('New Customers'),
            'url'       => $this->getUrl('adminhtml/*/customersNewest', array('_current'=>true)),
            'class'     => 'ajax'
        ));

        $this->addTab('customers', array(
            'label'     => __('Customers'),
            'url'       => $this->getUrl('adminhtml/*/customersMost', array('_current'=>true)),
            'class'     => 'ajax'
        ));

        return parent::_prepareLayout();
    }
}
