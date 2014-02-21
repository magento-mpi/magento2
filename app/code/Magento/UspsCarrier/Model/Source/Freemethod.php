<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UspsCarrier\Model\Source;

class Freemethod extends Method
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        array_unshift($options, ['value' => '', 'label' => __('None')]);
        return $options;
    }
}
