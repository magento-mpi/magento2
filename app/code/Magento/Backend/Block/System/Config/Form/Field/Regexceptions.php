<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend system config array field renderer
 */
class Magento_Backend_Block_System_Config_Form_Field_Regexceptions
    extends Magento_Backend_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var Magento_Data_Form_Element_Factory
     */
    protected $_elementFactory;

    /**
     * @var Magento_Core_Model_Theme_LabelFactory
     */
    protected $_labelFactory;

    /**
     * @param Magento_Data_Form_Element_Factory $elementFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param Magento_Core_Model_Theme_LabelFactory $labelFactory
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Element_Factory $elementFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        Magento_Core_Model_Theme_LabelFactory $labelFactory,
        array $data = array()
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_labelFactory = $labelFactory;
        parent::__construct($coreData, $context, $application, $data);
    }

    /**
     * Initialise form fields
     */
    protected function _construct()
    {
        $this->addColumn('search', array(
            'label' => __('Search String'),
            'style' => 'width:120px',
        ));
        $this->addColumn('value', array(
            'label' => __('Design Theme'),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Exception');
        parent::_construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'value' && isset($this->_columns[$columnName])) {
            /** @var $label Magento_Core_Model_Theme_Label */
            $label = $this->_labelFactory->create();
            $options = $label->getLabelsCollection(__('-- No Theme --'));
            $element = $this->_elementFactory->create('select');
            $element
                ->setForm($this->getForm())
                ->setName($this->_getCellInputElementName($columnName))
                ->setHtmlId($this->_getCellInputElementId('#{_id}', $columnName))
                ->setValues($options);
            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    }

}
