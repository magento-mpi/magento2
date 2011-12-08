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
 * Model for working with system.xml module files
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Config_System extends Mage_Core_Model_Config_Base
{
    function __construct($sourceData=null)
    {
        parent::__construct($sourceData);
    }

    public function load($module)
    {
        $file = Mage::getConfig()->getModuleDir('etc', $module).DS.'system.xml';
        $this->loadFile($file);
        return $this;
    }
}
