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

class Block extends \Magento\App\Action\Action
{
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

        $layout = $this->_view->getLayout();
        foreach ($blocks as $blockName) {
            $blockInstance = $layout->getBlock($blockName);
            if (is_object($blockInstance)) {
                $data[$blockName] = $blockInstance->toHtml();
            }
        }

        $layout->setIsPrivate();

        $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     * Returns block content as part of ESI request from Varnish
     */
    public function wrapesiAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $handles = unserialize($request->getParam('handles', serialize(array())));
        $blockName = $request->getParam('blockname', '');
        $ttl = $request->getParam('ttl', 0);

        if (!$handles || !$blockName) {
            return;
        }
        $data = '';

        $this->_view->loadLayout($handles);
        $blockInstance = $this->_view->getLayout()->getBlock($blockName);
        if (is_object($blockInstance)) {
            $data = $blockInstance->toHtml();
        }

        $response->appendBody($data);
        $response->setHeader('X-Magento-Ttl', $ttl);


    }
}
