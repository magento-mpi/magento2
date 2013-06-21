<?php
/**
 * Object manager configurator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_ObjectManager_Configurator extends Mage_Core_Model_ObjectManager_ConfigAbstract
{
    /**
     * Configure di instance
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function configure(Magento_ObjectManager $objectManager)
    {
        $objectManager->configure(array(
            'Enterprise_PageCache_Model_Cache' => array(
                'parameters' => array('config' => array('instance' => 'Mage_Core_Model_Config_Proxy'))
            ),
            'Enterprise_PageCache_Model_Request_Identifier' => array(
                'parameters' => array('scopeCode' => $this->_getParam(Mage::PARAM_RUN_CODE, '')),
            ),
        ));
    }
}
