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
    /**
     * @var array
     */
    protected $_options;

    /**
     * Initialize the options array
     */
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

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_options;
    }
}
