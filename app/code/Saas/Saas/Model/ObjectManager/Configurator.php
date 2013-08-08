<?php
/**
 * Object Manager configurator
 *
 * {license_notice}
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_ObjectManager_Configurator implements Magento_Core_Model_ObjectManager_DynamicConfigInterface
{
    /**
     * Retrieve runtime environment specific di configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        return array(
            'Saas_Saas_Model_DisabledConfiguration_Config' => array(
                'parameters' => array('plainList' => Saas_Saas_Model_DisabledConfiguration_Config::getPlainList())
            ),
        );
    }
}
