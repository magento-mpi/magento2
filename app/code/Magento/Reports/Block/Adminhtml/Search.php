<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml;

/**
 * Adminhtml search report page content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Search extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize Grid Container
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reports';
        $this->_controller = 'adminhtml_search';
        $this->_headerText = __('Search Terms');
        parent::_construct();
        $this->_removeButton('add');
    }
}
