<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form element renderer to display font picker element for VDE
 *
 * @method array getOptions()
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_FontPicker setOptions(array $options)
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_FontPicker setCssClass($class)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_FontPicker extends Magento_Data_Form_Element_Select
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'font-picker';

    /**
     * Default options which can be limited further by element's 'options' data
     *
     * @var array
     */
    protected $_defaultOptions = array(
        'Arial, Helvetica, sans-serif',
        'Verdana, Geneva, sans-serif',
        'Tahoma, Geneva, sans-serif',
        'Georgia, serif',
    );

    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();

        /*
        $options = array_intersect(array_combine($this->_defaultOptions, $this->_defaultOptions), $this->getOptions());
        $this->setOptions($options);
        */
        $this->setCssClass('element-' . self::CONTROL_TYPE);
    }
}

