<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Usa\Model\Shipping\Carrier\Ups\Source;

class Pickup extends \Magento\Usa\Model\Shipping\Carrier\Ups\Source\Generic
{
    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = 'pickup';

    public function toOptionArray()
    {
        $ups = $this->_shippingUps->getCode($this->_code);
        $arr = array();
        foreach ($ups as $k => $v) {
            $arr[] = array('value'=>$k, 'label'=>__($v['label']));
        }
        return $arr;
    }
}
