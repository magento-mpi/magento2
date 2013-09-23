<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usa\Model\Shipping\Carrier\Dhl\Source;

class Generic implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Usa\Model\Shipping\Carrier\Dhl
     */
    protected $_shippingDhl;

    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = '';

    /**
     * @param \Magento\Usa\Model\Shipping\Carrier\Dhl $shippingDhl
     */
    public function __construct(\Magento\Usa\Model\Shipping\Carrier\Dhl $shippingDhl)
    {
        $this->_shippingDhl = $shippingDhl;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configData = $this->_shippingDhl->getCode($this->_code);
        $arr = array();
        foreach ($configData as $code => $title) {
            $arr[] = array('value' => $code, 'label' => $title);
        }
        return $arr;
    }
}
