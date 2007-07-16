<?php

class Mage_Catalog_Block_Product_Search_Autocomplete extends Mage_Core_Block_Abstract
{
    public function toHtml()
    {
        $query = $this->getRequest()->getParam('query', '');
        $searchCollection = Mage::getResourceModel('catalog/search_collection');
        $searchCollection->addFieldToFilter('search_query', array('like'=>$query.'%'));
        $searchCollection->setOrder('popularity', 'desc');
        $searchCollection->setPageSize(20);
        $searchCollection->loadData();
        
        $html = '<ul>';
        foreach ($searchCollection->getItems() as $item) {
            $html .= '<li title="'.$item->getSearchQuery().'"><div style="float:right">'.$item->getNumResults().'</div>'.$item->getSearchQuery().'</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}