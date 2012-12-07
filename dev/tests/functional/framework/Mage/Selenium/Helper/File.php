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

use Symfony\Component\Yaml\Yaml;

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
     * @return array
     */
    public function loadYamlFile($fullFileName)
    {
        $data = array();
        if ($fullFileName && file_exists($fullFileName)) {
            $data = Yaml::parse($fullFileName);
        }
        return ($data) ? $data : array();
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
                if (!empty($fileData)) {
                    $data = array_replace_recursive($data, $fileData);
                }
            }
        }
        return $data;
    }
}
