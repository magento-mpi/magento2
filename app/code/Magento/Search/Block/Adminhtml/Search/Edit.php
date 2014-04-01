<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block\Adminhtml\Search;

/**
 * Search queries relations grid container
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Enable grid container
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Search';
        $this->_controller = 'adminhtml_search';
        $this->_headerText = __('Related Search Terms');
        $this->_addButtonLabel = __('Add New Search Term');
        parent::_construct();
        $this->_removeButton('add');
    }
}
