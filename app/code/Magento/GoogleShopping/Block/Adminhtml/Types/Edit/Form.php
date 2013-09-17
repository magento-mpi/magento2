<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Google Content types mapping form block
 */

class Magento_GoogleShopping_Block_Adminhtml_Types_Edit_Form extends Magento_Backend_Block_Widget_Form
{
    /**
     * @var Magento_GoogleShopping_Helper_Category|null
     */
    protected $_googleShoppingCategory = null;

    /**
     * @var Magento_Data_Form_Element_Factory
     */
    protected $_elementFactory;

    /**
     * @var Magento_Data_Form_Factory
     */
    protected $_formFactory;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Data_Form_Element_Factory $elementFactory
     * @param Magento_GoogleShopping_Helper_Category $googleShoppingCategory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Magento_Data_Form_Element_Factory $elementFactory,
        Magento_GoogleShopping_Helper_Category $googleShoppingCategory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_googleShoppingCategory = $googleShoppingCategory;
        $this->_elementFactory = $elementFactory;
        $this->_formFactory = $formFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_GoogleShopping_Block_Adminhtml_Types_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();

        $itemType = $this->getItemType();

        $fieldset = $form->addFieldset('content_fieldset', array(
            'legend'    => __('Attribute set mapping')
        ));

        if ( !($targetCountry = $itemType->getTargetCountry()) ) {
            $isoKeys = array_keys($this->_getCountriesArray());
            $targetCountry = isset($isoKeys[0]) ? $isoKeys[0] : null;
        }
        $countrySelect = $fieldset->addField('select_target_country', 'select', array(
            'label'     => __('Target Country'),
            'title'     => __('Target Country'),
            'name'      => 'target_country',
            'required'  => true,
            'options'   => $this->_getCountriesArray(),
            'value'     => $targetCountry,
        ));
        if ($itemType->getTargetCountry()) {
            $countrySelect->setDisabled(true);
        }

        $attributeSetsSelect = $this->getAttributeSetsSelectElement($targetCountry)
            ->setValue($itemType->getAttributeSetId());
        if ($itemType->getAttributeSetId()) {
            $attributeSetsSelect->setDisabled(true);
        }

        $fieldset->addField('attribute_set', 'note', array(
            'label'     => __('Attribute Set'),
            'title'     => __('Attribute Set'),
            'required'  => true,
            'text'      => '<div id="attribute_set_select">' . $attributeSetsSelect->toHtml() . '</div>',
        ));

        $categories = $this->_googleShoppingCategory->getCategories();
        $fieldset->addField('category', 'select', array(
            'label'     => __('Google Product Category'),
            'title'     => __('Google Product Category'),
            'required'  => true,
            'name'      => 'category',
            'options'   => array_combine($categories, array_map('htmlspecialchars_decode', $categories)),
            'value'      => $itemType->getCategory(),
        ));

        $attributesBlock = $this->getLayout()
            ->createBlock('Magento_GoogleShopping_Block_Adminhtml_Types_Edit_Attributes')
            ->setTargetCountry($targetCountry);
        if ($itemType->getId()) {
            $attributesBlock->setAttributeSetId($itemType->getAttributeSetId())
                ->setAttributeSetSelected(true);
        }

        $attributes = $this->_coreRegistry->registry('attributes');
        if (is_array($attributes) && count($attributes) > 0) {
            $attributesBlock->setAttributesData($attributes);
        }

        $fieldset->addField('attributes_box', 'note', array(
            'label'     => __('Attributes Mapping'),
            'text'      => '<div id="attributes_details">' . $attributesBlock->toHtml() . '</div>',
        ));

        $form->addValues($itemType->getData());
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setAction($this->getSaveUrl());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get Select field with list of available attribute sets for some target country
     *
     * @param  string $targetCountry
     * @return Magento_Data_Form_Element_Select
     */
    public function getAttributeSetsSelectElement($targetCountry)
    {
        $field = $this->_elementFactory->create('select');
        $field->setName('attribute_set_id')
            ->setId('select_attribute_set')
            ->setForm($this->_formFactory->create())
            ->addClass('required-entry')
            ->setValues($this->_getAttributeSetsArray($targetCountry));
        return $field;
    }

    /**
     * Get allowed country names array
     *
     * @return array
     */
    protected function _getCountriesArray()
    {
        $_allowed = Mage::getSingleton('Magento_GoogleShopping_Model_Config')->getAllowedCountries();
        $result = array();
        foreach ($_allowed as $iso => $info) {
            $result[$iso] = $info['name'];
        }
        return $result;
    }

    /**
     * Get array with attribute setes which available for some target country
     *
     * @param  string $targetCountry
     * @return array
     */
    protected function _getAttributeSetsArray($targetCountry)
    {
        $entityType = Mage::getModel('Magento_Catalog_Model_Product')->getResource()->getEntityType();
        $collection = Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection')
            ->setEntityTypeFilter($entityType->getId());

        $ids = array();
        $itemType = $this->getItemType();
        if (!($itemType instanceof Magento_Object && $itemType->getId())) {
            $typesCollection = Mage::getResourceModel('Magento_GoogleShopping_Model_Resource_Type_Collection')
                ->addCountryFilter($targetCountry)
                ->load();
            foreach ($typesCollection as $type) {
                $ids[] = $type->getAttributeSetId();
            }
        }

        $result = array('' => '');
        foreach ($collection as $attributeSet) {
            if (!in_array($attributeSet->getId(), $ids)) {
                $result[$attributeSet->getId()] = $attributeSet->getAttributeSetName();
            }
        }
        return $result;
    }

    /**
     * Get current attribute set mapping from register
     *
     * @return Magento_GoogleShopping_Model_Type
     */
    public function getItemType()
    {
        return $this->_coreRegistry->registry('current_item_type');
    }

    /**
     * Get URL for saving the current map
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('type_id' => $this->getItemType()->getId()));
    }
}
