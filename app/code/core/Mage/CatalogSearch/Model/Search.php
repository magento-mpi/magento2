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


class Mage_CatalogSearch_Model_Search extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        $this->_init('catalogsearch/search');
    }
    
    public function loadByQuery($query)
    {
    	$this->getResource()->loadByQuery($this, $query);
    	return $this;
    }
    
    public function updateSearch($query=null, $numResults=null)
    {
    	if (!is_null($query)) {
        	$this->getResource()->loadByQuery($this, $query);
	        if (!$this->getSearchId()) {
	            $this->setSearchQuery($query);
	        }
    	}
    	
    	if (!$this->getSearchQuery()) {
    		return $this;
    	}
    	
        if (!is_null($numResults)) {
        	$this->setNumResults($numResults);
        }
        
        $this->setPopularity($this->getPopularity()+1);
        
        $this->save();
        
        return $this;
    }
    
    public function getResourceCollection()
    {
    	return Mage::getResourceModel('catalogsearch/search_collection');
    }
}