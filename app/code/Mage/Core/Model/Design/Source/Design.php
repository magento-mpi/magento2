<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for eav attribute custom_design
 */
class Mage_Core_Model_Design_Source_Design extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Model_Theme_Label
     */
    protected $_themeLabel;

    /**
     * @param Mage_Core_Helper_Data $helper
     * @param Mage_Core_Model_Theme_Label $themeLabel
     */
    public function __construct(Mage_Core_Helper_Data $helper, Mage_Core_Model_Theme_Label $themeLabel)
    {
        $this->_helper = $helper;
        $this->_themeLabel = $themeLabel;
    }

    /**
     * Retrieve All Design Theme Options
     *
     * @param bool $withEmpty add empty (please select) values to result
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $label = $withEmpty ? __('-- Please Select --') : $withEmpty;
        return $this->_options = $this->_themeLabel->getLabelsCollection($label);
    }
}
