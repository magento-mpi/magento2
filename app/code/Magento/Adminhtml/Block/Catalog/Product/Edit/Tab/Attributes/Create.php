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
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes_Create extends Magento_Backend_Block_Widget_Button
{
    /**
     * Config of create new attribute
     *
     * @var Magento_Object
     */
    protected $_config = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager_Proxy
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrive config of new attribute creation
     *
     * @return Magento_Object
     */
    public function getConfig()
    {
        if (is_null($this->_config)) {
           $this->_config = new Magento_Object();
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
        $this->_eventManager->dispatch('adminhtml_catalog_product_edit_tab_attributes_create_html_before', array('block' => $this));
        if (!$this->getCanShow()) {
            return '';
        }

        return parent::_toHtml();
    }

    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }
} // Class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes_Create End
