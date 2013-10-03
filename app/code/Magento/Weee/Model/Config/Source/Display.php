<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Model\Config\Source;

class Display implements \Magento\Core\Model\Option\ArrayInterface
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
                'value' => \Magento\Weee\Model\Tax::DISPLAY_INCL,
                'label' => __('Including FPT only')
            ),
            array(
                'value' => \Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR,
                'label' => __('Including FPT and FPT description')
            ),
            array(
                'value' => \Magento\Weee\Model\Tax::DISPLAY_EXCL_DESCR_INCL,
                'label' => __('Excluding FPT, FPT description, final price')
            ),
            array(
                'value' => \Magento\Weee\Model\Tax::DISPLAY_EXCL,
                'label' => __('Excluding FPT')
            ),
        );
    }

}
