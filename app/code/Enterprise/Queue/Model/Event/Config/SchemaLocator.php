<?php
/**
 * Event observers configuration schema locator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Event_Config_SchemaLocator extends Mage_Core_Model_Event_Config_SchemaLocator
{
    /**
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(Mage_Core_Model_Config_Modules_Reader $moduleReader)
    {
        $this->_schema = $moduleReader->getModuleDir('etc', 'Enterprise_Queue') . DIRECTORY_SEPARATOR . 'event.xsd';
    }
 }
