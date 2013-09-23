<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Admin search controller(ajax grid)
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Controller_Adminhtml_Catalog_Search extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * Query factory
     *
     * @var Magento_CatalogSearch_Model_QueryFactory
     */
    protected $_queryFactory;

    /**
     * Construct
     *
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_CatalogSearch_Model_QueryFactory $queryFactory
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_CatalogSearch_Model_QueryFactory $queryFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_queryFactory = $queryFactory;
    }

    /**
     * Ajax grid action
     */
    public function relatedGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var Magento_CatalogSearch_Model_Query $model */
        $model = $this->_queryFactory->create();

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('This search no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_catalog_search', $model);

        $this->loadLayout(false);
        $this->renderLayout();
    }
}
