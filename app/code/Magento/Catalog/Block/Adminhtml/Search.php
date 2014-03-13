<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml;

/**
 * Catalog price rules
 *
 * @category    Magento
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Search extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'catalog_search';
        $this->_headerText = __('Search');
        $this->_addButtonLabel = __('Add New Search Term');
        parent::_construct();
    }
}
