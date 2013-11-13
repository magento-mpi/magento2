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
     * @param RequestInterface $request
     * @return mixed
     */
    public function dispatch(RequestInterface $request)
    {
        if(!$this->_objectManager->get('Magento\Core\Model\Store\Config')->getConfig('catalog/seo/search_terms')) {
            $this->_redirect('noroute');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    public function popularAction()
    {
        $this->_layoutServices->loadLayout();
        $this->_layoutServices->renderLayout();
    }
}
