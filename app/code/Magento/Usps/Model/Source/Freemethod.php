<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Usps\Model\Source;

/**
 * Freemethod source
 */
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
