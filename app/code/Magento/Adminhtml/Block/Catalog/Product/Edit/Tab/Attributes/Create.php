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
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Attributes;

class Create extends \Magento\Backend\Block\Widget\Button
{
    /**
     * Config of create new attribute
     *
     * @var \Magento\Object
     */
    protected $_config = null;

    /**
     * Retrive config of new attribute creation
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
                            '*/catalog_product_attribute/new',
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
                '*/catalog_product_attribute/new',
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

    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }
} // Class \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Attributes\Create End
