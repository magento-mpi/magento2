<?php
/**
 * Magento application object manager. Configures and application application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManager extends Magento_ObjectManager_ObjectManager
{

    /**
     * @param Magento_ObjectManager_Definition $definition
     */
    public function __construct(Magento_ObjectManager_Definition $definitions)
    {
        parent::__construct($definitions);
        Mage::setObjectManager($this);
    }
}
