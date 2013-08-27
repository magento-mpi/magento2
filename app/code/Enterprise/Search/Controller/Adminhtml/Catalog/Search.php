<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Admin search controller(ajax grid)
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Controller_Adminhtml_Catalog_Search extends Magento_Adminhtml_Controller_Action
{
    /**
     * Ajax grid action
     */
    public function relatedGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('Magento_CatalogSearch_Model_Query');

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

        Mage::register('current_catalog_search', $model);

        $this->loadLayout(false);
        $this->renderLayout();
    }
}
