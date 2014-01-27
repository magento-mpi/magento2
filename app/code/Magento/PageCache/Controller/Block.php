<?php
/**
 * PageCache controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Controller;

use Magento\App\Action\Context;

class Block extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\PageCache\Helper\Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param \Magento\PageCache\Helper\Data $helper
     */
    public function __construct(\Magento\App\Action\Context $context, \Magento\PageCache\Helper\Data $helper)
    {
        parent::__construct($context);
        $this->helper = $helper;
    }

    /**
     * Returns block content depends on ajax request
     */
    public function renderAction()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');
            return;
        }
        $blocks = $this->getRequest()->getParam('blocks', array());
        $handles = $this->getRequest()->getParam('handles', array());

        if (!$handles || !$blocks) {
            return;
        }
        $this->_view->loadLayout($handles);
        $data = array();

        foreach ($blocks as $blockName) {
            $blockInstance = $this->_view->getLayout()->getBlock($blockName);
            if (is_object($blockInstance)) {
                $data[$blockName] = $blockInstance->toHtml();
            }
        }

        $this->setPrivateHeaders();

        $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     * Set header parameters for private cache
     */
    protected function setPrivateHeaders()
    {
        $maxAge = $this->helper->getMaxAgeCache();

        $this->getResponse()->setHeader('cache-control', 'private, max-age=' . $maxAge, true);
        $this->getResponse()->setHeader(
            'expires',
            gmdate('D, d M Y H:i:s T', strtotime('+' . $maxAge . ' seconds')),
            true
        );
    }
}
