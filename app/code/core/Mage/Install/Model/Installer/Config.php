<?php
/**
 * Config installer
 *
 * @package     Mage
 * @subpackage  Install
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_Model_Installer_Config extends Mage_Install_Model_Installer 
{
    public function __construct() 
    {
        
    }
    
    public function getFormData()
    {
        $data = new Varien_Object();
        $data->setHost($_SERVER['HTTP_HOST'])
            ->setBasePath(substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'install/')))
            ->setPort(80)
            ->setScurePort(443);
        return $data;
    }
}