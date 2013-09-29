<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Payment\Model\Config\Source;

class Allspecificcountries implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>__('All Allowed Countries')),
            array('value'=>1, 'label'=>__('Specific Countries')),
        );
    }
}
