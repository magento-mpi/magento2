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

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

class Term extends \Magento\App\Action\Action
{
    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\Core\Model\Store\Config')->getConfig('catalog/seo/search_terms')) {
            $this->_redirect('noroute');
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    public function popularAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
