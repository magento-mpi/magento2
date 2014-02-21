<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ups\Model\Config\Source;

/**
 * Class Pickup
 */
class Pickup extends \Magento\Ups\Model\Config\Source\Generic
{
    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = 'pickup';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $ups = $this->carrierConfig->getCode($this->_code);
        $arr = array();
        foreach ($ups as $k => $v) {
            $arr[] = array('value'=>$k, 'label'=>__($v['label']));
        }
        return $arr;
    }
}
