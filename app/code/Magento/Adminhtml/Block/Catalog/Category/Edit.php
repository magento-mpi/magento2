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
namespace Magento\Adminhtml\Block\Catalog\Category;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
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
