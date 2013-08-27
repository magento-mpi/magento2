<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_System_Config_Source_Apply
{
    protected $_options;

    public function __construct()
    {
        $this->_options = array(
            array(
                'value' => 0,
                'label' => __('Before Discount')
            ),
            array(
                'value' => 1,
                'label' => __('After Discount')
            ),
        );
    }

    public function toOptionArray()
    {
        return $this->_options;
    }
}
