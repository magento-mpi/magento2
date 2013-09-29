<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Config\Source;

class Catalog implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>__('No (price without tax)')),
            array('value'=>1, 'label'=>__('Yes (only price with tax)')),
            array('value'=>2, 'label'=>__("Both (without and with tax)")),
        );
    }

}
