<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attribute add/edit form main tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain;

class Main extends AbstractMain
{
    /**
     * Adding product form elements for editing attribute
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        /* @var $form \Magento\Framework\Data\Form */
        $form = $this->getForm();
        /* @var $fieldset \Magento\Framework\Data\Form\Element\Fieldset */
        $fieldset = $form->getElement('base_fieldset');
        $fiedsToRemove = array('attribute_code', 'is_unique', 'frontend_class');

        foreach ($fieldset->getElements() as $element) {
            /** @var \Magento\Framework\Data\Form\AbstractForm $element  */
            if (substr($element->getId(), 0, strlen('default_value')) == 'default_value') {
                $fiedsToRemove[] = $element->getId();
            }
        }
        foreach ($fiedsToRemove as $id) {
            $fieldset->removeField($id);
        }

        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = array(array('value' => 'price', 'label' => __('Price')));
        $additionalReadOnlyTypes = array('media_image' => __('Media Image'), 'gallery' => __('Gallery'));
        if (isset($additionalReadOnlyTypes[$attributeObject->getFrontendInput()])) {
            $additionalTypes[] = array(
                'value' => $attributeObject->getFrontendInput(),
                'label' => $additionalReadOnlyTypes[$attributeObject->getFrontendInput()]
            );
        }

        $response = new \Magento\Framework\Object();
        $response->setTypes(array());
        $this->_eventManager->dispatch('adminhtml_product_attribute_types', array('response' => $response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
        }
        $this->_coreRegistry->register('attribute_type_hidden_fields', $_hiddenFields);

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        return $this;
    }

    /**
     * Retrieve additional element types for product attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array('apply' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Apply');
    }
}
