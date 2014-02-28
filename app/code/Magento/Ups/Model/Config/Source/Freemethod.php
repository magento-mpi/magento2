<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ups\Model\Config\Source;

/**
 * Class Freemethod
 */
class Freemethod extends \Magento\Ups\Model\Config\Source\Method
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $arr = parent::toOptionArray();
        array_unshift($arr, array('value'=>'', 'label'=>__('None')));
        return $arr;
    }
}
