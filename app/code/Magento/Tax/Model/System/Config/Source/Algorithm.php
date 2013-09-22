<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\System\Config\Source;

class Algorithm implements \Magento\Core\Model\Option\ArrayInterface
{
    protected $_options;

    public function __construct()
    {
        $this->_options = array(
            array(
                'value' => \Magento\Tax\Model\Calculation::CALC_UNIT_BASE,
                'label' => __('Unit Price')
            ),
            array(
                'value' => \Magento\Tax\Model\Calculation::CALC_ROW_BASE,
                'label' => __('Row Total')
            ),
            array(
                'value' => \Magento\Tax\Model\Calculation::CALC_TOTAL_BASE,
                'label' => __('Total')
            ),
        );
    }

    public function toOptionArray()
    {
        return $this->_options;
    }
}
