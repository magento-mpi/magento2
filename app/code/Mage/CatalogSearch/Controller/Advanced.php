<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Search Controller
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @module     Catalog
 */
class Mage_CatalogSearch_Controller_Advanced extends Magento_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Mage_CatalogSearch_Model_Session');
        $this->renderLayout();
    }

    public function resultAction()
    {
        $this->loadLayout();
        try {
            Mage::getSingleton('Mage_CatalogSearch_Model_Advanced')->addFilters($this->getRequest()->getQuery());
        } catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Mage_CatalogSearch_Model_Session')->addError($e->getMessage());
            $this->_redirectError(
                Mage::getModel('Magento_Core_Model_Url')
                    ->setQueryParams($this->getRequest()->getQuery())
                    ->getUrl('*/*/')
            );
        }
        $this->_initLayoutMessages('Mage_Catalog_Model_Session');
        $this->renderLayout();
    }
}
