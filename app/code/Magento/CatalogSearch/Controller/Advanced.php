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
            Mage::getSingleton('Magento_CatalogSearch_Model_Advanced')->addFilters($this->getRequest()->getQuery());
        } catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_CatalogSearch_Model_Session')->addError($e->getMessage());
            $this->_redirectError(
                Mage::getModel('Magento_Core_Model_Url')
                    ->setQueryParams($this->getRequest()->getQuery())
                    ->getUrl('*/*/')
            );
        }
        $this->_initLayoutMessages('Magento_Catalog_Model_Session');
        $this->renderLayout();
    }
}
