<?php
/**
 * Currency model
 *
 * @package     Mage
 * @subpackage  Directory
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Model_Currency extends Varien_Object
{
    public function __construct() 
    {
        
    }
    
    public function getResource()
    {
        return Mage::getSingleton('durectory_resource', 'currency');
    }
    
    public function load()
    {
        
    }
    
    public function save()
    {
        
    }
    
    public function delete()
    {
        
    }
    
    public function format()
    {
        
    }
}
