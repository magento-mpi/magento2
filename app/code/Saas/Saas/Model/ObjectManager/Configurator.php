<?php
/**
 * Object Manager configurator
 *
 * {license_notice}
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_ObjectManager_Configurator extends Mage_Core_Model_ObjectManager_ConfigAbstract
{
    /**
     * Configure di instance
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function configure(Magento_ObjectManager $objectManager)
    {
        $objectManager->configure(array(
            'Saas_Saas_Model_Maintenance_Config' => array(
                'parameters' => array('config' => $this->_getParam('maintenance_mode', array()))
            ),
            'Saas_Saas_Model_DisabledConfiguration_Config' => array(
                'parameters' => array('plainList' => Saas_Saas_Model_DisabledConfiguration_Config::getPlainList())
            ),
        ));
    }
}
