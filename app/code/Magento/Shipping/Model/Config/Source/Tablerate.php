<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Shipping\Model\Config\Source;

class Tablerate implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Shipping\Model\Carrier\Tablerate
     */
    protected $_carrierTablerate;

    /**
     * @param \Magento\Shipping\Model\Carrier\Tablerate $carrierTablerate
     */
    public function __construct(\Magento\Shipping\Model\Carrier\Tablerate $carrierTablerate)
    {
        $this->_carrierTablerate = $carrierTablerate;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $arr = array();
        foreach ($this->_carrierTablerate->getCode('condition_name') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
