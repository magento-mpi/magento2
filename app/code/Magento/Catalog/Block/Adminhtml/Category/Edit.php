<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Catalog\Block\Adminhtml\Category;

/**
 * Category container block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var string
     */
    protected $_template = 'catalog/category/edit.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Magento_Catalog';
        $this->_controller = 'adminhtml_category';
        $this->_mode = 'edit';
        parent::_construct();
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
    }
}
