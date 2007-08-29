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


class Mage_CatalogSearch_Model_Mysql4_Search_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    protected function _construct()
    {
        $this->_init('catalogsearch/search');
    }
    
    public function setQueryFilter($query)
    {
    	$this->getSelect()->reset(Zend_Db_Select::FROM)->distinct(true)
    		->from(
    			array('main_table'=>$this->getTable('catalogsearch/search')), 
    			array('search_query'=>"if(ifnull(synonim_for,'')<>'',synonim_for,search_query)", 'num_results')
    		)
    		->where('num_results>0 and search_query like ?', $query.'%')
    		->order('popularity desc');
print_r($this->getSelect()->__toString());
		return $this;
    }
}