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

class Advanced extends \Magento\Core\Controller\Front\Action
{

    /**
     * Url factory
     *
     * @var \Magento\Core\Model\UrlFactory
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
     * @var \Magento\Core\Model\Session\Generic
     */
    protected $_catalogSearchSession;

    /**
     * Construct
     *
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Session\Generic $catalogSearchSession
     * @param \Magento\CatalogSearch\Model\Advanced $catalogSearchAdvanced
     * @param \Magento\Core\Model\UrlFactory $urlFactory
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Session\Generic $catalogSearchSession,
        \Magento\CatalogSearch\Model\Advanced $catalogSearchAdvanced,
        \Magento\Core\Model\UrlFactory $urlFactory
    ) {
        $this->_catalogSearchSession = $catalogSearchSession;
        $this->_catalogSearchAdvanced = $catalogSearchAdvanced;
        $this->_urlFactory = $urlFactory;
        parent::__construct($context);
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento\CatalogSearch\Model\Session');
        $this->renderLayout();
    }

    public function resultAction()
    {
        $this->loadLayout();
        try {
            $this->_catalogSearchAdvanced->addFilters($this->getRequest()->getQuery());
        } catch (\Magento\Core\Exception $e) {
            $this->_catalogSearchSession->addError($e->getMessage());
            $this->_redirectError(
                $this->_urlFactory->create()
                    ->setQueryParams($this->getRequest()->getQuery())
                    ->getUrl('*/*/')
            );
        }
        $this->_initLayoutMessages('Magento\Catalog\Model\Session');
        $this->renderLayout();
    }
}
