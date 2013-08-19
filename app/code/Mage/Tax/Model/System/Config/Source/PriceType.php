<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tax_Model_System_Config_Source_PriceType
{
    protected $_options;

    public function __construct()
    {
        $this->_options = array(
            array(
                'value' => 0,
                'label' => __('Excluding Tax')
            ),
            array(
                'value' => 1,
                'label' => __('Including Tax')
            ),
        );
    }

    public function toOptionArray()
    {
        return $this->_options;
    }
}
