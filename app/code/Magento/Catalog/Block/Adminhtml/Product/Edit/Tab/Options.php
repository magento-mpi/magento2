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

use Magento\Backend\Block\Widget;

class Options extends Widget
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/edit/options.phtml';

    /**
     * @return Widget
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'add_button',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Add New Option'), 'class' => 'add', 'id' => 'add_new_defined_option')
        );

        $this->addChild('options_box', 'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option');

        $this->addChild(
            'import_button',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Import Options'), 'class' => 'add', 'id' => 'import_new_defined_option')
        );

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * @return string
     */
    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }
}
