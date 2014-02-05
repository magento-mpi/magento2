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

use Magento\PageCache\Helper\Data;

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
}
