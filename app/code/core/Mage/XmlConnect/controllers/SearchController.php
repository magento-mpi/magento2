<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect search controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_SearchController extends Mage_XmlConnect_Controller_Action
{

    /**
     * Declare content type header
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
    }

    /**
     * Perform search products
     */
    public function indexAction()
    {
        $_helper = Mage::helper('catalogsearch');
        $this->getRequest()->setParam($_helper->getQueryParamName(), $this->getRequest()->getParam('query'));

        $query = $_helper->getQuery();
        /* @var $query Mage_CatalogSearch_Model_Query */

        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText()) {
            if ($_helper->isMinQueryLength()) {
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

            $_helper->checkNotes();

            if (!$_helper->isMinQueryLength()) {
                $query->save();
            }
        }

        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Retrive suggestions based on search query
     */
    public function suggestAction()
    {
        $this->getRequest()->setParam('q', $this->getRequest()->getParam('query'));
        $this->loadLayout(false);
        $this->renderLayout();
    }
}