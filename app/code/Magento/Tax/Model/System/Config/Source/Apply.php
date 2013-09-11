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

class Apply
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
