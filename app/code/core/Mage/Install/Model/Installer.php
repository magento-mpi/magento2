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
    protected $_localConfigFile;
    public function __construct() 
    {
        $this->_localConfigFile = Mage::getBaseDir('etc').DS.'local.xml';
    }
    
    public function install()
    {
        
    }
    
    protected function _createLocalXml()
    {
        $templateFile = Mage::getBaseDir('etc').DS.'local.xml.template';
        file_put_contents($this->_localConfigFile, file_get_contents($templateFile));
    }
}