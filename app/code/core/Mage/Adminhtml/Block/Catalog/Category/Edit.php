<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category container block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
