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
 * Product attributes tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes extends Magento_Adminhtml_Block_Catalog_Form
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Load Wysiwyg on demand and prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::helper('Magento_Catalog_Helper_Data')->isModuleEnabled('Magento_Cms')
            && Mage::getSingleton('Magento_Cms_Model_Wysiwyg_Config')->isEnabled()
        ) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * Prepare attributes form
     *
     * @return null
     */
    protected function _prepareForm()
    {
        /** @var $group Magento_Eav_Model_Entity_Attribute_Group */
        $group = $this->getGroup();
        if ($group) {
            $form = new Magento_Data_Form();
            $product = $this->_coreRegistry->registry('product');
            $isWrapped = $this->_coreRegistry->registry('use_wrapper');
            if (!isset($isWrapped)) {
                $isWrapped = true;
            }
            $isCollapsable = $isWrapped && $group->getAttributeGroupCode() == 'product-details';
            $legend = $isWrapped ? __($group->getAttributeGroupName()) : null;
            // Initialize product object as form property to use it during elements generation
            $form->setDataObject($product);

            $fieldset = $form->addFieldset(
                'group-fields-' .$group->getAttributeGroupCode(),
                 array(
                    'class' => 'user-defined',
                    'legend' => $legend,
                    'collapsable' => $isCollapsable
                )
            );

            $attributes = $this->getGroupAttributes();

            $this->_setFieldset($attributes, $fieldset, array('gallery'));

            $urlKey = $form->getElement('url_key');
            if ($urlKey) {
                $urlKey->setRenderer(
                    $this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Form_Renderer_Attribute_Urlkey')
                );
            }

            $tierPrice = $form->getElement('tier_price');
            if ($tierPrice) {
                $tierPrice->setRenderer(
                    $this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier')
                );
            }

            $groupPrice = $form->getElement('group_price');
            if ($groupPrice) {
                $groupPrice->setRenderer(
                    $this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group')
                );
            }

            $recurringProfile = $form->getElement('recurring_profile');
            if ($recurringProfile) {
                $recurringProfile->setRenderer(
                    $this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Recurring')
                );
            }

            // Add new attribute controls if it is not an image tab
            if (!$form->getElement('media_gallery')
                && $this->_authorization->isAllowed('Magento_Catalog::attributes_attributes')
                && $isWrapped
            ) {
                $attributeCreate = $this->getLayout()
                    ->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes_Create');

                $attributeCreate->getConfig()
                    ->setAttributeGroupCode($group->getAttributeGroupCode())
                    ->setTabId('group_' . $group->getId())
                    ->setGroupId($group->getId())
                    ->setStoreId($form->getDataObject()->getStoreId())
                    ->setAttributeSetId($form->getDataObject()->getAttributeSetId())
                    ->setTypeId($form->getDataObject()->getTypeId())
                    ->setProductId($form->getDataObject()->getId());

                $attributeSearch = $this->getLayout()
                    ->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes_Search')
                    ->setGroupId($group->getId())
                    ->setGroupCode($group->getAttributeGroupCode());

                $attributeSearch->setAttributeCreate($attributeCreate->toHtml());

                $fieldset->setHeaderBar($attributeSearch->toHtml());
            }

            $values = $product->getData();

            // Set default attribute values for new product or on attribute set change
            if (!$product->getId() || $product->dataHasChangedFor('attribute_set_id')) {
                foreach ($attributes as $attribute) {
                    if (!isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                    }
                }
            }

            if ($product->hasLockedAttributes()) {
                foreach ($product->getLockedAttributes() as $attribute) {
                    $element = $form->getElement($attribute);
                    if ($element) {
                        $element->setReadonly(true, true);
                    }
                }
            }
            $form->addValues($values);
            $form->setFieldNameSuffix('product');

            Mage::dispatchEvent('adminhtml_catalog_product_edit_prepare_form', array('form' => $form));

            $this->setForm($form);
        }
    }

    /**
     * Retrieve additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $result = array(
            'price'    => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Price',
            'weight'   => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight',
            'gallery'  => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery',
            'image'    => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Image',
            'boolean'  => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Boolean',
            'textarea' => 'Magento_Adminhtml_Block_Catalog_Helper_Form_Wysiwyg',
        );

        $response = new Magento_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_catalog_product_edit_element_types', array('response' => $response));

        foreach ($response->getTypes() as $typeName => $typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }
}
