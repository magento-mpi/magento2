<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mage Core convert model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Convert extends Mage_Dataflow_Model_Convert_Profile_Collection
{
    public function __construct()
    {
        $classArr = explode('_', get_class($this));
        $moduleName = $classArr[0].'_'.$classArr[1];
        $etcDir = Mage::getConfig()->getModuleDir('etc', $moduleName);

        $fileName = $etcDir.DS.'convert.xml';
        if (is_readable($fileName)) {
            $data = file_get_contents($fileName);
            $this->importXml($data);
        }
    }

    public function getClassNameByType($type)
    {
        return Mage::getConfig()->getModelClassName($type);
    }
}
