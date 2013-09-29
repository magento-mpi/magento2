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
 * Adminhtml product attribute edit page tabs
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Attribute\Edit;

class Tabs extends \Magento\Adminhtml\Block\Widget\Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'main',
            array(
                'label'     => __('Properties'),
                'title'     => __('Properties'),
                'content'   => $this->getChildHtml('main'),
                'active'    => true
            )
        );
        $this->addTab(
            'labels',
            array(
                'label' => __('Manage Labels'),
                'title' => __('Manage Labels'),
                'content' => $this->getChildHtml('labels'),
            )
        );
        $this->addTab(
            'front',
            array(
                'label' => __('Frontend Properties'),
                'title' => __('Frontend Properties'),
                'content' => $this->getChildHtml('front'),
            )
        );

        return parent::_beforeToHtml();
    }

}
