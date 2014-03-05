<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Dhl\Model\Source\Method;

class Generic
{
    /**
     * @var \Magento\Dhl\Model\Carrier
     */
    protected $_shippingDhl;

    /**
     * @var string
     */
    protected $_code = '';

    /**
     * @param \Magento\Dhl\Model\Carrier $shippingDhl
     */
    public function __construct(\Magento\Dhl\Model\Carrier $shippingDhl)
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
