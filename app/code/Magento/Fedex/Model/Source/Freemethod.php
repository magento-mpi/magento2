<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Fedex\Model\Source;

/**
 * Fedex freemethod source implementation
 *
 * @category   Magento
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Freemethod
    extends \Magento\Fedex\Model\Source\Method
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $arr = parent::toOptionArray();
        array_unshift($arr, array('value' => '', 'label' => __('None')));
        return $arr;
    }
}
