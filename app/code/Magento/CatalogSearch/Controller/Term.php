<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\CatalogSearch\Controller;

class Term extends \Magento\Core\Controller\Front\Action {

    public function preDispatch(){
        parent::preDispatch();
        if(!$this->_objectManager->get('Magento\Core\Model\Store\Config')->getConfig('catalog/seo/search_terms')){
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
