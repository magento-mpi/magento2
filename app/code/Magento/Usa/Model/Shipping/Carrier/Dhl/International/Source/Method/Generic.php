<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usa\Model\Shipping\Carrier\Dhl\International\Source\Method;

class Generic
{
    /**
     * @var \Magento\Usa\Model\Shipping\Carrier\Dhl\International
     */
    protected $_shippingDhlInt;

    /**
     * @var string
     */
    protected $_code = '';

    /**
     * @param \Magento\Usa\Model\Shipping\Carrier\Dhl\International $shippingDhlInt
     */
    public function __construct(\Magento\Usa\Model\Shipping\Carrier\Dhl\International $shippingDhlInt)
    {
        $this->_shippingDhlInt = $shippingDhlInt;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configData = $this->_shippingDhlInt->getCode($this->_code);
        $arr = array();
        foreach ($configData as $code => $title) {
            $arr[] = array('value' => $code, 'label' => $title);
        }
        return $arr;
    }
}
