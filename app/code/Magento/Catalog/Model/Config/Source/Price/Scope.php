<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source\Price;

use Magento\Core\Model\Option\ArrayInterface;

class Scope implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>__('Global')),
            array('value'=>'1', 'label'=>__('Website')),
        );
    }
}
