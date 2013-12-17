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
 * customers defined options
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab;

class Options extends \Magento\Backend\Block\Widget
{
    protected $_template = 'catalog/product/edit/options.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('add_button', 'Magento\Backend\Block\Widget\Button', array(
            'label' => __('Add New Option'),
            'class' => 'add',
            'id'    => 'add_new_defined_option'
        ));

        $this->addChild('options_box', 'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option');

        $this->addChild('import_button', 'Magento\Backend\Block\Widget\Button', array(
            'label' => __('Import Options'),
            'class' => 'add',
            'id'    => 'import_new_defined_option'
        ));

        return parent::_prepareLayout();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }
}
