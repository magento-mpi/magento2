<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for eav attribute custom_design
 */
class Magento_Core_Model_Design_Source_Design extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @var Magento_Core_Model_Theme_Label
     */
    protected $_themeLabel;

    /**
     * @param Magento_Core_Model_Theme_Label $themeLabel
     */
    public function __construct(Magento_Core_Model_Theme_Label $themeLabel)
    {
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
