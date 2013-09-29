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
 * Adminhtml search report page content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Report;

class Search extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize Grid Container
     *
     */
    protected function _construct()
    {
        $this->_controller = 'report_search';
        $this->_headerText = __('Search Terms');
        parent::_construct();
        $this->_removeButton('add');
    }
}
