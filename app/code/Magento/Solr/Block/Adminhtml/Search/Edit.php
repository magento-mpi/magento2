<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Block\Adminhtml\Search;

/**
 * Search queries relations grid container
 *
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
        $this->_blockGroup = 'Magento_Solr';
        $this->_controller = 'adminhtml_search';
        $this->_headerText = __('Related Search Terms');
        $this->_addButtonLabel = __('Add New Search Term');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
