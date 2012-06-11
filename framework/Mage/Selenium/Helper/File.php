<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once('SymfonyComponents/YAML/sfYaml.php');

/**
 * File helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_File extends Mage_Selenium_Helper_Abstract
{
    /**
     * Loads YAML file and returns parsed data
     *
     * @param string $fullFileName Full file name (including path)
     *
     * @return array|bool
     */
    public function loadYamlFile($fullFileName)
    {
        $data = false;
        if ($fullFileName && file_exists($fullFileName)) {
            $data = sfYaml::load($fullFileName);
        }
        return $data;
    }

    /**
     * Loads multiple YAML files and returns merged data
     *
     * @param string $globExpr File names glob pattern
     *
     * @return array
     */
    public function loadYamlFiles($globExpr)
    {
        $data = array();
        $files = glob($globExpr);
        if (!empty($files)) {
            foreach ($files as $file) {
                $fileData = $this->loadYamlFile($file);
                if ($fileData) {
                    $data = array_replace_recursive($data, $fileData);
                }
            }
        }
        return $data;
    }
}
