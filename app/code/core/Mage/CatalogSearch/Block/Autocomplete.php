<?php

class Mage_CatalogSearch_Block_Autocomplete extends Mage_Core_Block_Abstract
{
    public function toHtml()
    {
		if (!$this->_beforeToHtml()) {
			return '';
		}

        $query = $this->getRequest()->getParam('query', '');
        $searchCollection = Mage::getResourceModel('catalogsearch/search_collection')
            ->addFieldToFilter('search_query', array('like'=>$query.'%'))
            ->setOrder('popularity', 'desc')
            ->setPageSize(20);
            
        $searchCollection
            ->getSelect()->orWhere('synonims regexp ?', '(^|,)\s*'.$query.'.*(,|$)');
            
        $searchCollection
            ->loadData();
        
        $i=0;
        $html = '<ul>';
        foreach ($searchCollection->getItems() as $item) {
            $html .= '<li title="'.$item->getSearchQuery().'" class="'.((++$i)%2?'odd':'even').'"><div style="float:right">'.$item->getNumResults().'</div>'.$item->getSearchQuery().'</li>';
        }
        $html .= '</ul>';
        
        return $html;
    }
}