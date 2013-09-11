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
namespace Magento\CatalogSearch\Controller;

class Result extends \Magento\Core\Controller\Front\Action
{
    /**
     * Retrieve catalog session
     *
     * @return \Magento\Catalog\Model\Session
     */
    protected function _getSession()
    {
        return \Mage::getSingleton('Magento\Catalog\Model\Session');
    }
    /**
     * Display search result
     */
    public function indexAction()
    {
        $query = \Mage::helper('Magento\CatalogSearch\Helper\Data')->getQuery();
        /* @var $query \Magento\CatalogSearch\Model\Query */

        $query->setStoreId(\Mage::app()->getStore()->getId());

        if ($query->getQueryText() != '') {
            if (\Mage::helper('Magento\CatalogSearch\Helper\Data')->isMinQueryLength()) {
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

            \Mage::helper('Magento\CatalogSearch\Helper\Data')->checkNotes();

            $this->loadLayout();
            $this->_initLayoutMessages('Magento\Catalog\Model\Session');
            $this->_initLayoutMessages('Magento\Checkout\Model\Session');
            $this->renderLayout();

            if (!\Mage::helper('Magento\CatalogSearch\Helper\Data')->isMinQueryLength()) {
                $query->save();
            }
        }
        else {
            $this->_redirectReferer();
        }
    }
}
