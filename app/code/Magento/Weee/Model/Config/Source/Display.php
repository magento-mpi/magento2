<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Weee_Model_Config_Source_Display implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve list of available options to display FPT
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Weee_Model_Tax::DISPLAY_INCL,
                'label' => __('Including FPT only')
            ),
            array(
                'value' => Magento_Weee_Model_Tax::DISPLAY_INCL_DESCR,
                'label' => __('Including FPT and FPT description')
            ),
            array(
                'value' => Magento_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL,
                'label' => __('Excluding FPT, FPT description, final price')
            ),
            array(
                'value' => Magento_Weee_Model_Tax::DISPLAY_EXCL,
                'label' => __('Excluding FPT')
            ),
        );
    }

}
