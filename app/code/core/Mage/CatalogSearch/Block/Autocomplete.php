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


class Mage_CatalogSearch_Block_Autocomplete extends Mage_Core_Block_Abstract
{
    public function toHtml()
    {
		if (!$this->_beforeToHtml()) {
			return '';
		}

        $query = $this->getRequest()->getParam('query', '');
        $searchCollection = Mage::getResourceModel('catalogsearch/search_collection')
        	->setQueryFilter($query)
            ->setPageSize(20);

        $searchCollection->loadData();
        $items = $searchCollection->getItems();

        if (sizeof($items)==0) {
        	return '';
        }
        if (sizeof($items)>0) {
        	$found = false;
        	foreach ($items as $i=>$item) {
        		if ($item->getSearchQuery()==$query) {
        			$found = true;
        			unset($items[$i]);
        			array_unshift($items, $item);
        		}
        	}
        	/*
        	if (!$found) {
	        	$default = Mage::getModel('catalogsearch/search')->setSearchQuery($query);
	        	array_unshift($items, $default);
        	}
        	*/
        }
        $i=0;
        $html = '<ul><li style="display:none"></li>';
        foreach ($items as $item) {
            $html .= '<li title="'.$item->getSearchQuery().'" class="'.((++$i)%2?'odd':'even').'"><div style="float:right">'.$item->getNumResults().'</div>'.$item->getSearchQuery().'</li>';
        }
        $html .= '</ul>';
        
        return $html;
    }
}