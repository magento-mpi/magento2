<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Dashboard;

/**
 * Adminhtml dashboard diagram tabs
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Diagrams extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('diagram_tab');
        $this->setDestElementId('diagram_tab_content');
    }

    /**
     * @return $this
     */
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
