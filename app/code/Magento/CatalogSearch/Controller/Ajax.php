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

class Ajax extends \Magento\App\Action\Action
{
    /**
     * Url
     *
     * @var \Magento\Core\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Url $url
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Url $url
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
        $this->_layoutServices->loadLayout(false);
        $this->_layoutServices->renderLayout();
    }
}
