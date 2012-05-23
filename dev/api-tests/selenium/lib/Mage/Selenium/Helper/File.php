<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once('SymfonyComponents/YAML/sfYaml.php');

/**
 * File helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     {license_link}
 */
class Mage_Selenium_Helper_File extends Mage_Selenium_Helper_Abstract
{
    /**
     * Loads YAML file and returns parsed data
     *
     * @param string $fullFileName Full file name (including path)
     *
     * @return array|false
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
     * Load multiple YAML files and return merged data
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
