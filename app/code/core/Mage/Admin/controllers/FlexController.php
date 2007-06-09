<?php
/**
 * Reports flex admin controller
 *
 * @package    Mage
 * @subpackage Reports
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Ivan Chepurnyi <mitch@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */

class Mage_Admin_FlexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() 
    {
        $block = Mage::getModel('core', 'layout')->createBlock('core/template', 'flex')
                ->setTemplate('admin/reports/flex.phtml');
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
	
	public function timelineAction()
    {
         $this->getResponse()->setBody( Mage::getModel('test_data')->getTimelineData() );
    }
    
    
    public function citiesAction()
    {
         $this->getResponse()->setBody( Mage::getModel('test_data') -> getUsersCities( $this->getRequest()->getPost('country', '') ) );
    }
	
	public function linearExampleAction()
	{
		if( $this->getRequest()->getPost('refreshDraw', '') )
		{
			$this->getResponse()->setBody( Mage::getModel('test_data')-> getNewLinearData() );
		}
		else
			$this->getResponse()->setBody( Mage::getModel('test_data')-> getAllLinearExample() );
	}

}// Class IndexController END