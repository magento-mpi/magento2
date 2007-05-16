<?php
/**
 * Reports flex admin controller
 *
 * @package    Mage
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
       $this->getResponse()->setBody( Mage::getModel('reports_config') -> getGlobalConfig() );  
    }
	
	public function languageAction()
    {
       $this->getResponse()->setBody( Mage::getModel('reports_config') -> getLanguage() );
    }
	
	public function dashboardAction()
    {
       $this->getResponse()->setBody( Mage::getModel('reports_config') -> getDashboard() );
    }
	
	public function countriesAction()
	{
		 $this->getResponse()->setBody( Mage::getModel('test_data') -> getUsersCountries() );
	}
	
	public function citiesAction()
	{
		 $this->getResponse()->setBody( Mage::getModel('test_data') -> getUsersCities( $this->getRequest()->getPost('country', '') ) );
	}

}// Class IndexController END