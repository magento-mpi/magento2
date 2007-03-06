<?php



/**
 * Catalog Search Controller
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_SearchController extends Mage_Core_Controller_Front_Action
{
    public function resultAction() 
    {
        if ($searchQuery = $this->_getSearchQuery()) {
            Mage::getBlock('search.form.mini')->assign('query', $searchQuery);
            $searchResBlock = Mage::createBlock('catalog_search_result', 'search.result', array('query'=>$searchQuery));
            $searchResBlock->loadData($this->getRequest());
            
            Mage::getBlock('content')->append($searchResBlock);
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