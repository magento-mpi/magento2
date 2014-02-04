<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Refresh;

/**
 * Report Refresh statistic container
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Statistics extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Modify Header and remove button "Add"
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reports';
        $this->_controller = 'adminhtml_refresh_statistics';
        $this->_headerText = __('Refresh Statistics');
        parent::_construct();
        $this->_removeButton('add');
    }
}
