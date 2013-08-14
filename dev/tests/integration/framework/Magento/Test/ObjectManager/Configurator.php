<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Test_ObjectManager_Configurator implements Magento_Core_Model_ObjectManager_DynamicConfigInterface
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
                'Magento_Core_Model_Cookie' => 'Magento_Test_Cookie'
            )
        );
    }
}
