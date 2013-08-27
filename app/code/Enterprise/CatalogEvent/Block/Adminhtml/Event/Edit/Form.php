<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Events edit form
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{

    /**
     * Adminhtml data
     *
     * @var Magento_Adminhtml_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param Magento_Adminhtml_Helper_Data $adminhtmlData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Data $adminhtmlData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        parent::__construct($context, $data);
    }

    /**
     * Return form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
    }

    /**
     * Prepares layout, set custom renderers
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        Magento_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Enterprise_CatalogEvent_Block_Adminhtml_Form_Renderer_Fieldset_Element',
                $this->getNameInLayout() . '_fieldset_element_catalog_event'
            )
        );
    }

    /**
     * Prepares event edit form
     *
     * @return Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(
            array(
                'id'      => 'edit_form',
                'action'  => $this->getActionUrl(),
                'method'  => 'post',
                'field_name_suffix' => 'catalogevent',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setHtmlIdPrefix('event_edit_');

        $fieldset = $form->addFieldset('general_fieldset',
            array(
                'legend' => __('Catalog Event Information'),
                'class'  => 'fieldset-wide'
            )
        );

        $this->_addElementTypes($fieldset);

        $currentCategory = Mage::getModel('Magento_Catalog_Model_Category')
            ->load($this->getEvent()->getCategoryId());

        $fieldset->addField('category_name', 'note',
            array(
                'id'    => 'category_span',
                'label' => __('Category')
            )
        );

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $timeFormat = Mage::app()->getLocale()->getTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);

        $fieldset->addField('date_start', 'date', array(
                'label'        => __('Start Date'),
                'name'         => 'date_start',
                'required'     => true,
                'image'        => $this->getViewFileUrl('images/grid-cal.gif'),
                'date_format'  => $dateFormat,
                'time_format'  => $timeFormat
            ));

        $fieldset->addField('date_end', 'date', array(
                'label'        => __('End Date'),
                'name'         => 'date_end', 'required' => true,
                'image'        => $this->getViewFileUrl('images/grid-cal.gif'),
                'date_format'  => $dateFormat,
                'time_format'  => $timeFormat
            ));

        $fieldset->addField('image', 'image', array(
                'label' => __('Image'),
                'scope' => 'store',
                'name'  => 'image'
             )
        );

        $fieldset->addField('sort_order', 'text', array(
                'label' => __('Sort Order'),
                'name'  => 'sort_order',
                'class' => 'validate-num qty'
             )
        );

        $statuses = array(
            Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING => __('Upcoming'),
            Enterprise_CatalogEvent_Model_Event::STATUS_OPEN => __('Open'),
            Enterprise_CatalogEvent_Model_Event::STATUS_CLOSED => __('Closed')
        );

        $fieldset->addField('display_state_array', 'checkboxes', array(
                'label'  => __('Display Countdown Ticker On'),
                'name'   => 'display_state[]',
                'values' => array(
                    Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE => __('Category Page'),
                    Enterprise_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE => __('Product Page')
                )
            ));

        if ($this->getEvent()->getId()) {
            $fieldset->addField('status', 'note', array(
                    'label' => __('Status'),
                    'text'  => ($this->getEvent()->getStatus() ? $statuses[$this->getEvent()->getStatus()] : $statuses[Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING])
            ));
        }

        $form->setValues($this->getEvent()->getData());

        if ($currentCategory && $this->getEvent()->getId()) {
            $form->getElement('category_name')->setText(
                '<a href="' . $this->_adminhtmlData->getUrl('adminhtml/catalog_category/edit',
                                                            array('clear' => 1, 'id' => $currentCategory->getId()))
                . '">' . $currentCategory->getName() . '</a>'
            );
        } else {
            $form->getElement('category_name')->setText(
                '<a href="' . $this->getParentBlock()->getBackUrl()
                . '">' . $currentCategory->getName() . '</a>'
            );
        }

        $form->getElement('date_start')->setValue($this->getEvent()->getStoreDateStart());
        $form->getElement('date_end')->setValue($this->getEvent()->getStoreDateEnd());

        if ($this->getEvent()->getDisplayState()) {
            $form->getElement('display_state_array')->setChecked($this->getEvent()->getDisplayState());
        }

        $form->setUseContainer(true);
        $form->setDataObject($this->getEvent());
        $this->setForm($form);

        if ($this->getEvent()->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                if ($element->getId() !== 'image') {
                    $element->setReadonly(true, true);
                }
            }
        }

        if ($this->getEvent()->getImageReadonly()) {
            $form->getElement('image')->setReadonly(true, true);
        }
        return parent::_prepareForm();
    }

    /**
     * Retrieve catalog event model
     *
     * @return Enterprise_CatalogEvent_Model_Event
     */
    public function getEvent()
    {
        return Mage::registry('enterprise_catalogevent_event');
    }

    /**
     * Retrieve Additional Element Types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array('image' => 'Enterprise_CatalogEvent_Block_Adminhtml_Event_Helper_Image');
    }

}
