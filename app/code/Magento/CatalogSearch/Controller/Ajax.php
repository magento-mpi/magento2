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
class Magento_CatalogSearch_Controller_Ajax extends Magento_Core_Controller_Front_Action
{
    /**
     * Url
     *
     * @var Magento_Core_Model_Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Url $url
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Url $url
    ) {
        $this->_url = $url;
        parent::__construct($context);
    }

    public function suggestAction()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            $this->getResponse()->setRedirect($this->_url->getBaseUrl());
        }

        $this->addPageLayoutHandles();
        $this->loadLayout(false)->renderLayout();
    }
}
