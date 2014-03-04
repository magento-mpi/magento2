<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Backend\Model\Config\Source;

class Enabledisable implements \Magento\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>__('Enable')),
            array('value'=>0, 'label'=>__('Disable')),
        );
    }
}
