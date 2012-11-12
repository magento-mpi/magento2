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

use Zend\Di\InstanceManager;

class Magento_Test_Di_InstanceManager extends InstanceManager
{
    /**
     * Remove shared instance
     *
     * @param string $classOrAlias
     */
    public function removeSharedInstance($classOrAlias)
    {
        unset($this->sharedInstances[$classOrAlias]);
    }
}
