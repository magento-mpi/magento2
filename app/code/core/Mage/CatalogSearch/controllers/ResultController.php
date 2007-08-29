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
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Search Controller
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @module     Catalog
 */
class Mage_CatalogSearch_ResultController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() 
    {
        $searchQuery = $this->getRequest()->getParam('q', false);

        if ($searchQuery) {
        	$search = Mage::getModel('catalogsearch/search')->loadByQuery($searchQuery);
        	if (!$search->getId()) {
        		
        		$search->setSearchQuery($searchQuery)->updateSearch();
        		
        	} elseif ($search->getRedirect()) {
        		
	    		$search->updateSearch();
        		$this->getResponse()->setRedirect($search->getRedirect());
        		return;
        		
        	} elseif ($search->getSynonimFor()) {
        		
        		$search->updateSearch();
        		$searchQuery = $search->getSynonimFor();
        		
        	}
        }
        
        $this->loadLayout();
            

        $this->getLayout()->getBlock('top.search')->assign('query', $searchQuery);
        $searchResBlock = $this->getLayout()->createBlock('catalogsearch/result', 'search.result', array('query'=>$searchQuery));
        //$searchResBlock->loadByQuery($this->getRequest());

        $this->getLayout()->getBlock('content')->append($searchResBlock);
        
        $this->renderLayout();
    }
}
