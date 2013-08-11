<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Search Controller
 *
 * @category   Mage
 * @package    Magento_CatalogSearch
 * @module     Catalog
 */
class Magento_CatalogSearch_Controller_Ajax extends Magento_Core_Controller_Front_Action
{
    public function suggestAction()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            $this->getResponse()->setRedirect(Mage::getSingleton('Magento_Core_Model_Url')->getBaseUrl());
        }

        $this->addPageLayoutHandles();
        $this->loadLayout(false)->renderLayout();
    }
}
