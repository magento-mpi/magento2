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
 */
class Magento_CatalogSearch_Controller_Result extends Magento_Core_Controller_Front_Action
{
    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog session
     *
     * @var Magento_Catalog_Model_Session
     */
    protected $_catalogSession;

    /**
     * Construct
     *
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Catalog_Model_Session $catalogSession
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Catalog_Model_Session $catalogSession,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_catalogSession = $catalogSession;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Retrieve catalog session
     *
     * @return Magento_Catalog_Model_Session
     */
    protected function _getSession()
    {
        return $this->_catalogSession;
    }
    /**
     * Display search result
     */
    public function indexAction()
    {
        $query = $this->_objectManager->get('Magento_CatalogSearch_Helper_Data')->getQuery();
        /* @var $query Magento_CatalogSearch_Model_Query */

        $query->setStoreId($this->_storeManager->getStore()->getId());

        if ($query->getQueryText() != '') {
            if ($this->_objectManager->get('Magento_CatalogSearch_Helper_Data')->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            }
            else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity()+1);
                }
                else {
                    $query->setPopularity(1);
                }

                if ($query->getRedirect()){
                    $query->save();
                    $this->getResponse()->setRedirect($query->getRedirect());
                    return;
                }
                else {
                    $query->prepare();
                }
            }

            $this->_objectManager->get('Magento_CatalogSearch_Helper_Data')->checkNotes();

            $this->loadLayout();
            $this->_initLayoutMessages('Magento_Catalog_Model_Session');
            $this->_initLayoutMessages('Magento_Checkout_Model_Session');
            $this->renderLayout();

            if (!$this->_objectManager->get('Magento_CatalogSearch_Helper_Data')->isMinQueryLength()) {
                $query->save();
            }
        }
        else {
            $this->_redirectReferer();
        }
    }
}
