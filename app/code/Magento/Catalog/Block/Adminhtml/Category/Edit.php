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
 * Category container block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Category;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var string
     */
    protected $_template = 'catalog/category/edit.phtml';

    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Magento_Catalog';
        $this->_controller = 'adminhtml_category';
        $this->_mode = 'edit';
        parent::_construct();
    }
}
