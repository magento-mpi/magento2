<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Sales;

/**
 * Adminhtml refunded report page content block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Refunded extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var string
     */
    protected $_template = 'report/grid/container.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reports';
        $this->_controller = 'adminhtml_sales_refunded';
        $this->_headerText = __('Total Refunded Report');
        parent::_construct();

        $this->_removeButton('add');
        $this->addButton(
            'filter_form_submit',
            array('label' => __('Show Report'), 'onclick' => 'filterFormSubmit()', 'class' => 'primary')
        );
    }

    /**
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/refunded', array('_current' => true));
    }
}
