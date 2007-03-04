<?php

#include_once 'Ecom/Core/Controller/Zend/Action.php';

/**
 * Catalog Search Controller
 *
 * @package    Ecom
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Ecom_Catalog_SearchController extends Ecom_Core_Controller_Zend_Action
{
    public function resultAction() 
    {
        if ($searchQuery = $this->_getSearchQuery()) {
            Ecom::getBlock('search.form.mini')->assign('query', $searchQuery);
            $searchResBlock = Ecom::createBlock('catalog_search_result', 'search.result', array('query'=>$searchQuery));
            $searchResBlock->loadData($this->getRequest());
            
            Ecom::getBlock('content')->append($searchResBlock);
        }
        else {
            
        }
    }
    
    protected function _getSearchQuery()
    {
        if (!empty($_GET['q'])) {
            return $_GET['q'];
        }
        elseif($this->getRequest()->getParam('q'))
        {
            return $this->getRequest()->getParam('q');
        }
        return false;
    }
}