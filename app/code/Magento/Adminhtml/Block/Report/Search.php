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

class Magento_Adminhtml_Block_Report_Search extends Magento_Backend_Block_Widget_Grid_Container
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
