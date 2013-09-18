<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_TestFramework_ObjectManager_Configurator
    implements \Magento\Core\Model\ObjectManager\DynamicConfigInterface
{
    /**
     * Map application initialization params to Object Manager configuration format
     *
     * @return array
     */
    public function getConfiguration()
    {
        return array(
            'preferences' => array(
                'Magento\Core\Model\Cookie' => 'Magento_TestFramework_Cookie'
            )
        );
    }
}
