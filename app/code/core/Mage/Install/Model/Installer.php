<?php
/**
 * Installer
 *
 * @package     Mage
 * @subpackage  Install
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_Model_Installer
{
    public function __construct() 
    {
        
    }
    
    public function install()
    {
        
    }
    
    protected function _createLocalXml()
    {
        $templateFile = Mage::getBaseDir('etc').DS.'local.xml.template';
        $destFile     = Mage::getBaseDir('etc').DS.'local.xml';
        file_put_contents($destFile, file_get_contents($templateFile));
    }
}