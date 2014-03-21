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

use Magento\App\Action\Action;
use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;
use Magento\App\ResponseInterface;

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
        if (!$this->_objectManager->get('Magento\App\Config\ScopeConfigInterface')->getConfig('catalog/seo/search_terms')) {
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
