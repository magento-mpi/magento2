<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Main banner properties edit form
 *
 * @category   Enterprise
 * @package    Enterprise_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Properties extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Set form id prefix, add customer segment binding, set values if banner is editing
     *
     * @return Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Properties
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $htmlIdPrefix = 'banner_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $model = Mage::registry('current_banner');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('Enterprise_Banner_Helper_Data')->__('Banner Properties'))
        );

        if ($model->getBannerId()) {
            $fieldset->addField('banner_id', 'hidden', array(
                'name' => 'banner_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('Enterprise_Banner_Helper_Data')->__('Banner Name'),
            'name'      => 'name',
            'required'  => true,
            'disabled'  => (bool)$model->getIsReadonly()
        ));

        $fieldset->addField('is_enabled', 'select', array(
            'label'     => Mage::helper('Enterprise_Banner_Helper_Data')->__('Active'),
            'name'      => 'is_enabled',
            'required'  => true,
            'disabled'  => (bool)$model->getIsReadonly(),
            'options'   => array(
                Enterprise_Banner_Model_Banner::STATUS_ENABLED  => Mage::helper('Enterprise_Banner_Helper_Data')->__('Yes'),
                Enterprise_Banner_Model_Banner::STATUS_DISABLED => Mage::helper('Enterprise_Banner_Helper_Data')->__('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_enabled', Enterprise_Banner_Model_Banner::STATUS_ENABLED);
        }

        // whether to specify banner types - for UI design purposes only
        $fieldset->addField('is_types', 'select', array(
            'label'     => Mage::helper('Enterprise_Banner_Helper_Data')->__('Applies To'),
            'options'   => array(
                    '0' => Mage::helper('Enterprise_Banner_Helper_Data')->__('Any Banner Type'),
                    '1' => Mage::helper('Enterprise_Banner_Helper_Data')->__('Specified Banner Types'),
                ),
            'disabled'  => (bool)$model->getIsReadonly(),
        ));
        $model->setIsTypes((string)(int)$model->getTypes()); // see $form->setValues() below

        $fieldset->addField('types', 'multiselect', array(
            'label'     => Mage::helper('Enterprise_Banner_Helper_Data')->__('Specify Types'),
            'name'      => 'types',
            'disabled'  => (bool)$model->getIsReadonly(),
            'values'    => Mage::getSingleton('Enterprise_Banner_Model_Config')->toOptionArray(false, false),
            'can_be_empty' => true,
        ));

        // whether to specify customer segments - also for UI design purposes only
        $fieldset->addField('customer_segment_is_all', 'select', array(
            'label'     => Mage::helper('Enterprise_Banner_Helper_Data')->__('Customer Segments'),
            'options'   => array(
                    '1' => Mage::helper('Enterprise_Banner_Helper_Data')->__('Any'),
                    '0' => Mage::helper('Enterprise_Banner_Helper_Data')->__('Specified'),
                ),
            'note'      => Mage::helper('Enterprise_Banner_Helper_Data')->__('Applies to Any of the Specified Customer Segments'),
            'disabled'  => (bool)$model->getIsReadonly()
        ));
        $model->setCustomerSegmentIsAll($model->getCustomerSegmentIds() ? '0' : '1'); // see $form->setValues() below

        $resource = Mage::getResourceSingleton('Enterprise_CustomerSegment_Model_Resource_Segment_Collection');
        $fieldset->addField('customer_segment_ids', 'multiselect', array(
            'name'         => 'customer_segment_ids',
            'values'       => $resource->toOptionArray(),
            'can_be_empty' => true,
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        // define customer segments and types field dependencies
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Form_Element_Dependence')
                ->addFieldMap("{$htmlIdPrefix}is_types", 'is_types')
                ->addFieldMap("{$htmlIdPrefix}types", 'types')
                ->addFieldDependence('types', 'is_types', '1')
                ->addFieldMap("{$htmlIdPrefix}customer_segment_is_all", 'customer_segment_is_all')
                ->addFieldMap("{$htmlIdPrefix}customer_segment_ids", 'customer_segment_ids')
                ->addFieldDependence('customer_segment_ids', 'customer_segment_is_all', '0')
        );
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Enterprise_Banner_Helper_Data')->__('Banner Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
