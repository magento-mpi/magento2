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

class Advanced extends \Magento\App\Action\Action
{

    /**
     * Url factory
     *
     * @var \Magento\UrlFactory
     */
    protected $_urlFactory;

    /**
     * Catalog search advanced
     *
     * @var \Magento\CatalogSearch\Model\Advanced
     */
    protected $_catalogSearchAdvanced;

    /**
     * Catalog search session
     *
     * @var \Magento\Session\Generic
     */
    protected $_catalogSearchSession;

    /**
     * Construct
     *
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Session\Generic $catalogSearchSession
     * @param \Magento\CatalogSearch\Model\Advanced $catalogSearchAdvanced
     * @param \Magento\UrlFactory $urlFactory
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Session\Generic $catalogSearchSession,
        \Magento\CatalogSearch\Model\Advanced $catalogSearchAdvanced,
        \Magento\UrlFactory $urlFactory
    ) {
        $this->_catalogSearchSession = $catalogSearchSession;
        $this->_catalogSearchAdvanced = $catalogSearchAdvanced;
        $this->_urlFactory = $urlFactory;
        parent::__construct($context);
    }

    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    public function resultAction()
    {
        $this->_view->loadLayout();
        try {
            $this->_catalogSearchAdvanced->addFilters($this->getRequest()->getQuery());
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $defaultUrl = $this->_urlFactory->create()
                ->setQueryParams($this->getRequest()->getQuery())
                ->getUrl('*/*/');
            $this->getResponse()->setRedirect($this->_redirect->error($defaultUrl));
        }
        $this->_view->renderLayout();
    }
}
