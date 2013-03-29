<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fixture modules reader, which embeds fixture system.xml to whole configuration read
 */
class Saas_Mage_Backend_Adminhtml_System_Config_ModulesReader extends Mage_Core_Model_Config_Modules_Reader
{
    public function getModuleConfigurationFiles($filename)
    {
        $result = parent::getModuleConfigurationFiles($filename);
        if ($filename == 'adminhtml' . DIRECTORY_SEPARATOR . 'system.xml') {
            $result[] = __DIR__ . DIRECTORY_SEPARATOR . 'system.xml';
        }
        return $result;
    }
}
