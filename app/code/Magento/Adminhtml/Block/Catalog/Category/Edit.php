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
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Category_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * @var string
     */
    protected $_template = 'catalog/category/edit.phtml';

    protected function _construct()
    {
        $this->_objectId    = 'entity_id';
        $this->_controller  = 'catalog_category';
        $this->_mode        = 'edit';
        parent::_construct();
    }
}
