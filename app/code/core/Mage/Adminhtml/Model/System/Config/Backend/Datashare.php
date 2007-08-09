<?php
/**
 * Config category field backend
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Datashare
{
    public function beforeSave(Varien_Object $configData)
    {
echo "<pre>".print_r($configData,1)."</pre>"; die;
    }
}