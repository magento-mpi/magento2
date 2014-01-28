<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml most viewed products report content block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Reports\Block\Adminhtml\Product;

class Viewed extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var string
     */
    protected $_template = 'report/grid/container.phtml';

    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reports';
        $this->_controller = 'adminhtml_product_viewed';
        $this->_headerText = __('Most Viewed');
        parent::_construct();

        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => __('Show Report'),
            'onclick'   => 'filterFormSubmit()',
            'class'     => 'primary'
        ));
    }

    /**
     * Get filter url
     *
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/viewed', array('_current' => true));
    }
}
