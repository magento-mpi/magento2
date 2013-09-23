<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usa\Model\Shipping\Carrier\Usps\Source;

class Generic implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Usa\Model\Shipping\Carrier\Usps
     */
    protected $_shippingUsps;

    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = '';

    /**
     * @param \Magento\Usa\Model\Shipping\Carrier\Usps $shippingUsps
     */
    public function __construct(\Magento\Usa\Model\Shipping\Carrier\Usps $shippingUsps)
    {
        $this->_shippingUsps = $shippingUsps;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configData = $this->_shippingUsps->getCode($this->_code);
        $arr = array();
        foreach ($configData as $code => $title) {
            $arr[] = array('value' => $code, 'label' => $title);
        }
        return $arr;
    }
}
