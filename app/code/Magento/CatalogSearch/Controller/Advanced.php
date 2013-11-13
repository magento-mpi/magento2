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
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Session\Generic $catalogSearchSession
     * @param \Magento\CatalogSearch\Model\Advanced $catalogSearchAdvanced
     * @param \Magento\Core\Model\UrlFactory $urlFactory
     */
    public function __construct(
        \Magento\App\Action\Context $context,
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
        $this->_layoutServices->loadLayout();
        $this->_layoutServices->getLayout()->initMessages('Magento\CatalogSearch\Model\Session');
        $this->renderLayout();
    }

    public function resultAction()
    {
        $this->_layoutServices->loadLayout();
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
        $this->_layoutServices->getLayout()->initMessages('Magento\Catalog\Model\Session');
        $this->renderLayout();
    }
}
