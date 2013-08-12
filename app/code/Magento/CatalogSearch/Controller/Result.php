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
     * Retrieve catalog session
     *
     * @return Magento_Catalog_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Catalog_Model_Session');
    }
    /**
     * Display search result
     */
    public function indexAction()
    {
        $query = Mage::helper('Magento_CatalogSearch_Helper_Data')->getQuery();
        /* @var $query Magento_CatalogSearch_Model_Query */

        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText() != '') {
            if (Mage::helper('Magento_CatalogSearch_Helper_Data')->isMinQueryLength()) {
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

            Mage::helper('Magento_CatalogSearch_Helper_Data')->checkNotes();

            $this->loadLayout();
            $this->_initLayoutMessages('Magento_Catalog_Model_Session');
            $this->_initLayoutMessages('Magento_Checkout_Model_Session');
            $this->renderLayout();

            if (!Mage::helper('Magento_CatalogSearch_Helper_Data')->isMinQueryLength()) {
                $query->save();
            }
        }
        else {
            $this->_redirectReferer();
        }
    }
}
