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
class Magento_CatalogSearch_Controller_Advanced extends Magento_Core_Controller_Front_Action
{

    /**
     * Url factory
     *
     * @var Magento_Core_Model_UrlFactory
     */
    protected $_urlFactory;

    /**
     * Catalog search advanced
     *
     * @var Magento_CatalogSearch_Model_Advanced
     */
    protected $_catalogSearchAdvanced;

    /**
     * Catalog search session
     *
     * @var Magento_CatalogSearch_Model_Session
     */
    protected $_catalogSearchSession;

    /**
     * Construct
     *
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_CatalogSearch_Model_Session $catalogSearchSession
     * @param Magento_CatalogSearch_Model_Advanced $catalogSearchAdvanced
     * @param Magento_Core_Model_UrlFactory $urlFactory
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_CatalogSearch_Model_Session $catalogSearchSession,
        Magento_CatalogSearch_Model_Advanced $catalogSearchAdvanced,
        Magento_Core_Model_UrlFactory $urlFactory
    ) {
        $this->_catalogSearchSession = $catalogSearchSession;
        $this->_catalogSearchAdvanced = $catalogSearchAdvanced;
        $this->_urlFactory = $urlFactory;
        parent::__construct($context);
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_CatalogSearch_Model_Session');
        $this->renderLayout();
    }

    public function resultAction()
    {
        $this->loadLayout();
        try {
            $this->_catalogSearchAdvanced->addFilters($this->getRequest()->getQuery());
        } catch (Magento_Core_Exception $e) {
            $this->_catalogSearchSession->addError($e->getMessage());
            $this->_redirectError(
                $this->_urlFactory->create()
                    ->setQueryParams($this->getRequest()->getQuery())
                    ->getUrl('*/*/')
            );
        }
        $this->_initLayoutMessages('Magento_Catalog_Model_Session');
        $this->renderLayout();
    }
}
