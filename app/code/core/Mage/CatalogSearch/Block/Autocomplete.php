<?php

class Mage_CatalogSearch_Block_Autocomplete extends Mage_Core_Block_Abstract
{
    public function toHtml()
    {
        $query = $this->getRequest()->getParam('query', '');
        $searchCollection = Mage::getResourceModel('catalogsearch/search_collection')
            ->addFieldToFilter('search_query', array('like'=>$query.'%'))
            ->setOrder('popularity', 'desc')
            ->setPageSize(20)
            ->loadData();
        
        $html = '<ul>';
        foreach ($searchCollection->getItems() as $item) {
            $html .= '<li title="'.$item->getSearchQuery().'"><div style="float:right">'.$item->getNumResults().'</div>'.$item->getSearchQuery().'</li>';
        }
        $html .= '</ul>';
        
        return $html;
    }
}