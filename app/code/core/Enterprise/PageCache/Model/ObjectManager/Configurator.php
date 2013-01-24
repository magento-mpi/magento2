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
        /** @var $factory Enterprise_PageCache_Model_CacheFactory */
        $factory = $objectManager->get('Enterprise_PageCache_Model_CacheFactory');
        $arguments = array(
            'config' => $objectManager->get('Mage_Core_Model_Config_Primary'),
            'dirs' => $objectManager->get('Mage_Core_Model_Dir'),
            'helperFactory' => $objectManager->get('Mage_Core_Model_Factory_Helper'),
            'banCache' => $this->_getParam(Mage::PARAM_BAN_CACHE, false),
            'options' => $this->_getParam(Mage::PARAM_CACHE_OPTIONS, array())
        );
        $model = $factory->get($arguments);
        $objectManager->addSharedInstance($model, 'Mage_Core_Model_Cache');
        $objectManager->addSharedInstance($model, 'Enterprise_PageCache_Model_Cache');
        $objectManager->configure(array(
            'Enterprise_PageCache_Model_Processor' => array(
                'parameters' => array('scopeCode' => $this->_getParam(Mage::PARAM_RUN_CODE, '')),
            ),
        ));
    }
}
