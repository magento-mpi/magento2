<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_ObjectManager extends Magento_ObjectManager_Zend
{
    /**
     * Classes with xml properties to explicitly call __destruct() due to https://bugs.php.net/bug.php?id=62468
     *
     * @var array
     */
    protected $_classesToDestruct = array(
        'Mage_Core_Model_Config',
        'Mage_Core_Model_Layout',
        'Mage_Core_Model_Layout_Merge',
        'Mage_Core_Model_Layout_ScheduledStructure',
    );

    /**
     * Clear InstanceManager cache
     *
     * @return Magento_Test_ObjectManager
     */
    public function clearCache()
    {
        foreach ($this->_classesToDestruct as $className) {
            if ($this->hasSharedInstance($className)) {
                $object = $this->get($className);
                if ($object) {
                    // force to cleanup circular references
                    $object->__destruct();
                }
            }
        }

        $resource = $this->get('Mage_Core_Model_Resource');
        $this->_di->setInstanceManager(new Magento_Di_InstanceManager_Zend());
        $this->addSharedInstance($this, 'Magento_ObjectManager');
        $this->addSharedInstance($resource, 'Mage_Core_Model_Resource');

        return $this;
    }
}
