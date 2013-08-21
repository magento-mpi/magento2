<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_CatalogSearch_Controller_Term extends Magento_Core_Controller_Front_Action {

    public function preDispatch(){
        parent::preDispatch();
        if(!Mage::getStoreConfig('catalog/seo/search_terms')){
              $this->_redirect('noroute');
              $this->setFlag('',self::FLAG_NO_DISPATCH,true);
        }
        return $this;

    }
    public function popularAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
