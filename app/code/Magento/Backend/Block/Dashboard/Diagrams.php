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
 * Adminhtml dashboard diagram tabs
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\Dashboard;

class Diagrams extends \Magento\Backend\Block\Widget\Tabs
{

    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';

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
            'content'   => $this->getLayout()->createBlock('Magento\Backend\Block\Dashboard\Tab\Orders')->toHtml(),
            'active'    => true
        ));

        $this->addTab('amounts', array(
            'label'     => __('Amounts'),
            'content'   => $this->getLayout()->createBlock('Magento\Backend\Block\Dashboard\Tab\Amounts')->toHtml(),
        ));
        return parent::_prepareLayout();
    }
}
