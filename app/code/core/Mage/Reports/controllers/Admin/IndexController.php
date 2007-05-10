<?php
/**
 * Reports base admin controller
 *
 * @package    Ecom
 * @subpackage Reports
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */

class Mage_Reports_IndexController extends Mage_Core_Controller_Admin_Action
{
    function indexAction() 
    {
        $this->getResponse()->setBody('output XML');
    }

}// Class IndexController END