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
            'Enterprise_PageCache_Model_Processor' => array(
                'parameters' => array('scopeCode' => $this->_getParam(Mage::PARAM_RUN_CODE, '')),
            ),
        ));
    }
}
