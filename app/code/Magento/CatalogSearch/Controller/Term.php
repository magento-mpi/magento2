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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

class Term extends Action
{
    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('catalog/seo/search_terms', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $this->_redirect('noroute');
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * @return void
     */
    public function popularAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
