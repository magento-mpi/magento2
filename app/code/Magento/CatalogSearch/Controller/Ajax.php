<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Search Controller
 *
 * @category   Magento
 * @package    Magento_CatalogSearch
 * @module     Catalog
 */
namespace Magento\CatalogSearch\Controller;

class Ajax extends \Magento\Core\Controller\Front\Action
{
    public function suggestAction()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            $this->getResponse()->setRedirect(\Mage::getSingleton('Magento\Core\Model\Url')->getBaseUrl());
        }

        $this->addPageLayoutHandles();
        $this->loadLayout(false)->renderLayout();
    }
}
