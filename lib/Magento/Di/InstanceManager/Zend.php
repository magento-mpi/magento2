<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Zend\Di\InstanceManager;

class Magento_Di_InstanceManager_Zend extends InstanceManager implements Magento_Di_InstanceManager
{
    /**
     * Remove shared instance
     *
     * @param string $classOrAlias
     * @return Magento_Di_InstanceManager_Zend
     */
    public function removeSharedInstance($classOrAlias)
    {
        unset($this->sharedInstances[$classOrAlias]);

        return $this;
    }
}
