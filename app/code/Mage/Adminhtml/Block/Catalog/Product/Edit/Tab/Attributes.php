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
 * Product attributes tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Form
{
    /**
     * Load Wysiwyg on demand and prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::helper('Mage_Catalog_Helper_Data')->isModuleEnabled('Mage_Cms')
            && Mage::getSingleton('Mage_Cms_Model_Wysiwyg_Config')->isEnabled()
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
        /** @var $group Mage_Eav_Model_Entity_Attribute_Group */
        $group = $this->getGroup();
        if ($group) {
            $form = new Varien_Data_Form();
            $product = Mage::registry('product');
            $isWrapped = Mage::registry('use_wrapper');
            if (!isset($isWrapped)) {
                $isWrapped = true;
            }
            $isCollapsable = $isWrapped && $group->getAttributeGroupCode() == 'product-details';
            $legend = $isWrapped ? Mage::helper('Mage_Catalog_Helper_Data')->__($group->getAttributeGroupName()) : null;
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
                    $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Form_Renderer_Attribute_Urlkey')
                );
            }

            $tierPrice = $form->getElement('tier_price');
            if ($tierPrice) {
                $tierPrice->setRenderer(
                    $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier')
                );
            }

            $groupPrice = $form->getElement('group_price');
            if ($groupPrice) {
                $groupPrice->setRenderer(
                    $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group')
                );
            }

            $recurringProfile = $form->getElement('recurring_profile');
            if ($recurringProfile) {
                $recurringProfile->setRenderer(
                    $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Recurring')
                );
            }

            // Add new attribute button if it is not an image tab
            if (!$form->getElement('media_gallery')
                && Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Catalog::attributes_attributes')
                && $isWrapped
            ) {
                $headerBar = $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes_Create');

                $headerBar->getConfig()
                    ->setAttributeGroupCode($group->getAttributeGroupCode())
                    ->setTabId('group_' . $group->getId())
                    ->setGroupId($group->getId())
                    ->setStoreId($form->getDataObject()->getStoreId())
                    ->setAttributeSetId($form->getDataObject()->getAttributeSetId())
                    ->setTypeId($form->getDataObject()->getTypeId())
                    ->setProductId($form->getDataObject()->getId());

                $fieldset->setHeaderBar($headerBar->toHtml());
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
            'price'    => 'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Price',
            'weight'   => 'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight',
            'gallery'  => 'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery',
            'image'    => 'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Image',
            'boolean'  => 'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Boolean',
            'textarea' => 'Mage_Adminhtml_Block_Catalog_Helper_Form_Wysiwyg',
        );

        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_catalog_product_edit_element_types', array('response' => $response));

        foreach ($response->getTypes() as $typeName => $typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }
}
