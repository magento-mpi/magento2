<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Category;

/**
 * Category container block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
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
        $this->_removeButton('back');
        $this->_removeButton('reset');
        $this->_removeButton('save');
    }
}
