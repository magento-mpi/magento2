<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Reports flex admin controller
 *
 * @category   Mage
 * @package    Mage_AdminExt
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Admin_FlexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() 
    {
        $block = Mage::getModel('core/layout')->createBlock('core/template', 'flex')
                ->setTemplate('admin/reports/flex.phtml');
        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function configAction()
    {
       $this->getResponse()->setBody( Mage::getModel('reports_config/')->getGlobalConfig() );  
    }
    
    public function languageAction()
    {
       $this->getResponse()->setBody( Mage::getModel('reports_config/')->getLanguage() );
    }
    
    public function dashboardAction()
    {
       $this->getResponse()->setBody( Mage::getModel('reports_config/')->getDashboard() );
    }
    
    public function countriesAction()
    {
         $this->getResponse()->setBody( Mage::getModel('test_data/')->getUsersCountries() );
    }
	
	public function timelineAction()
    {
         $this->getResponse()->setBody( Mage::getModel('test_data/')->getTimelineData() );
    }
    
    
    public function citiesAction()
    {
         $this->getResponse()->setBody( Mage::getModel('test_data/')->getUsersCities( $this->getRequest()->getPost('country', '') ) );
    }
	
	public function linearExampleAction()
	{
		if( $this->getRequest()->getPost('refreshDraw', '') )
		{
			$this->getResponse()->setBody( Mage::getModel('test_data/')->getNewLinearData() );
		}
		else
			$this->getResponse()->setBody( Mage::getModel('test_data/')->getAllLinearExample() );
	}

}// Class IndexController END