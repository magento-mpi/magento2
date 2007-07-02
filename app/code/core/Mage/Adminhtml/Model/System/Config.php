<?php
/**
 * Configuration for control system
 * 
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <dmitriy@varien.com>
 */
class Mage_Adminhtml_Model_System_Config extends Varien_Simplexml_Config
{
    public function __construct()
    {
        parent::__construct();
        $this->setXml($this->loadFile(Mage::getModuleDir('etc', 'Mage_Adminhtml').DS.'system.xml'));
    }
}