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

use Magento\Framework\App\Action\Context;
use Magento\CatalogSearch\Model\Advanced as ModelAdvanced;
use Magento\Framework\Session\Generic;
use Magento\Framework\UrlFactory;

class Advanced extends \Magento\Framework\App\Action\Action
{
    /**
     * Url factory
     *
     * @var UrlFactory
     */
    protected $_urlFactory;

    /**
     * Catalog search advanced
     *
     * @var ModelAdvanced
     */
    protected $_catalogSearchAdvanced;

    /**
     * Catalog search session
     *
     * @var Generic
     */
    protected $_catalogSearchSession;

    /**
     * Construct
     *
     * @param Context $context
     * @param Generic $catalogSearchSession
     * @param ModelAdvanced $catalogSearchAdvanced
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        Context $context,
        Generic $catalogSearchSession,
        ModelAdvanced $catalogSearchAdvanced,
        UrlFactory $urlFactory
    ) {
        $this->_catalogSearchSession = $catalogSearchSession;
        $this->_catalogSearchAdvanced = $catalogSearchAdvanced;
        $this->_urlFactory = $urlFactory;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    /**
     * @return void
     */
    public function resultAction()
    {
        $this->_view->loadLayout();
        try {
            $this->_catalogSearchAdvanced->addFilters($this->getRequest()->getQuery());
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $defaultUrl = $this->_urlFactory->create()->addQueryParams(
                $this->getRequest()->getQuery()
            )->getUrl(
                '*/*/'
            );
            $this->getResponse()->setRedirect($this->_redirect->error($defaultUrl));
        }
        $this->_view->renderLayout();
    }
}
