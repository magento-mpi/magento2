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
            \Mage::getSingleton('Magento\CatalogSearch\Model\Advanced')->addFilters($this->getRequest()->getQuery());
        } catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento_CatalogSearch_Model_Session')->addError($e->getMessage());
            $this->_redirectError(
                \Mage::getModel('\Magento\Core\Model\Url')
                    ->setQueryParams($this->getRequest()->getQuery())
                    ->getUrl('*/*/')
            );
        }
        $this->_initLayoutMessages('\Magento\Catalog\Model\Session');
        $this->renderLayout();
    }
}
