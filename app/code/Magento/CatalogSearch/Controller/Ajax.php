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

use Magento\Framework\App\Action\Action;

class Ajax extends Action
{
    /**
     * @return void
     */
    public function suggestAction()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            $this->getResponse()->setRedirect($this->_url->getBaseUrl());
        }

        $suggestData = $this->_objectManager->get('Magento\CatalogSearch\Helper\Data')->getSuggestData();
        $this->getResponse()->setHeader('Content-type', 'application/json', true)->setBody(json_encode($suggestData));
    }
}
