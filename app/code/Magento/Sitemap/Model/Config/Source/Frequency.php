<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Sitemap\Model\Config\Source;

class Frequency implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'always', 'label'=>__('Always')),
            array('value'=>'hourly', 'label'=>__('Hourly')),
            array('value'=>'daily', 'label'=>__('Daily')),
            array('value'=>'weekly', 'label'=>__('Weekly')),
            array('value'=>'monthly', 'label'=>__('Monthly')),
            array('value'=>'yearly', 'label'=>__('Yearly')),
            array('value'=>'never', 'label'=>__('Never')),
        );
    }
}
