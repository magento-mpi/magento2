<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usa\Model\Shipping\Carrier\Fedex\Source;

class Generic implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Usa\Model\Shipping\Carrier\Fedex
     */
    protected $_shippingFedex;

    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = '';

    /**
     * @param \Magento\Usa\Model\Shipping\Carrier\Fedex $shippingFedex
     */
    public function __construct(\Magento\Usa\Model\Shipping\Carrier\Fedex $shippingFedex)
    {
        $this->_shippingFedex = $shippingFedex;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configData = $this->_shippingFedex->getCode($this->_code);
        $arr = array();
        foreach ($configData as $code => $title) {
            $arr[] = array('value' => $code, 'label' => $title);
        }
        return $arr;
    }
}
