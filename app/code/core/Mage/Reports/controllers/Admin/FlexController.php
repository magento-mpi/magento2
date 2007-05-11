<?php
/**
 * Reports flex admin controller
 *
 * @package    Ecom
 * @subpackage Reports
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */

class Mage_Reports_FlexController extends Mage_Core_Controller_Admin_Action
{
    public function indexAction() 
    {
        $block = Mage::getModel('core', 'layout')->createBlock('tpl', 'flex')
                ->setTemplate('reports/flex.phtml');
        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function configAction()
    {
        $this->getResponse()->setBody('');
    }

}// Class IndexController END