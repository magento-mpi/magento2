<?php
/**
 * Object manager configurator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_ObjectManager_Configurator implements Magento_ObjectManager_Configuration
{
    /**
     * Configure di instance
     *
     * @param Magento_ObjectManager $objectManager
     * @param array $runTimeParams
     */
    public function configure(Magento_ObjectManager $objectManager, array $runTimeParams = array())
    {
        $objectManager->configure(array(
            'Enterprise_PageCache_Model_Processor' => array(
                'parameters' => array('scopeCode' => $runTimeParams['runCode']),
            ),
        ));
    }
}
