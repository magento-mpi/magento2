<?php
/**
 * Json controller
 *
 * @package    Ecom
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_JsonController extends Mage_Core_Controller_Front_Action 
{
    public function __construct() 
    {
        $this->setFlag($action, 'no-defaultLayout', true);
    }
    
    public function regionsAction()
    {
        
    }
}