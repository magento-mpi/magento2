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

class Cctype implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options =  array();

        foreach (\Mage::getSingleton('Magento\Payment\Model\Config')->getCcTypes() as $code => $name) {
            $options[] = array(
               'value' => $code,
               'label' => $name
            );
        }

        return $options;
    }
}
