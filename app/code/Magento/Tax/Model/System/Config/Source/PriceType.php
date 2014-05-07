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

class PriceType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->_options = array(
            array('value' => 0, 'label' => __('Excluding Tax')),
            array('value' => 1, 'label' => __('Including Tax'))
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
