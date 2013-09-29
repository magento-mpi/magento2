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
 * Adminhtml cms blocks content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Report\Review;

class Customer extends \Magento\Adminhtml\Block\Widget\Grid\Container
{

    protected function _construct()
    {
        $this->_controller = 'report_review_customer';
        $this->_headerText = __('Customers Reviews');
        parent::_construct();
        $this->_removeButton('add');
    }

}
