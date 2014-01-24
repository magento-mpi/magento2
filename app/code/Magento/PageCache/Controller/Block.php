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
        if (!$this->_request->isAjax()) {
            $this->_forward('noroute');
            return;
        }
        $blocks = $this->getRequest()->getParam('blocks', false);
        $blocks = $blocks ? $blocks : array();

        $handles = $this->getRequest()->getParam('handles', false);
        $handles = $handles ? $handles : array();

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

        $this->_response->appendBody(json_encode($data));
    }
}
