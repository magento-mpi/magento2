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
 * New attribute panel on product edit page
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes;

use Magento\Backend\Block\Widget\Button;

class Create extends Button
{
    /**
     * Config of create new attribute
     *
     * @var \Magento\Object
     */
    protected $_config = null;

    /**
     * Retrieve config of new attribute creation
     *
     * @return \Magento\Object
     */
    public function getConfig()
    {
        if (is_null($this->_config)) {
           $this->_config = new \Magento\Object();
        }

        return $this->_config;
    }

    /**
     * @return Button
     */
    protected function _beforeToHtml()
    {
        $this->setId('create_attribute_' . $this->getConfig()->getGroupId())
            ->setType('button')
            ->setClass('action-add')
            ->setLabel(__('New Attribute'))
            ->setDataAttribute(array('mage-init' =>
                array('productAttributes' =>
                    array(
                        'url' => $this->getUrl(
                            'catalog/product_attribute/new',
                            array(
                                'group' => $this->getConfig()->getAttributeGroupCode(),
                                'store' => $this->getConfig()->getStoreId(),
                                'product' => $this->getConfig()->getProductId(),
                                'type' => $this->getConfig()->getTypeId(),
                                'popup' => 1
                            )
                        )
                    )
                )
            ));

        $this->getConfig()
            ->setUrl($this->getUrl(
                'catalog/product_attribute/new',
                array(
                    'group' => $this->getConfig()->getAttributeGroupCode(),
                    'store' => $this->getConfig()->getStoreId(),
                    'product' => $this->getConfig()->getProductId(),
                    'type' => $this->getConfig()->getTypeId(),
                    'popup' => 1
                )
            ));

        return parent::_beforeToHtml();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->setCanShow(true);
        $this->_eventManager->dispatch('adminhtml_catalog_product_edit_tab_attributes_create_html_before', array(
            'block' => $this,
        ));
        if (!$this->getCanShow()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }
}
