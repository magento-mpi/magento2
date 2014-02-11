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

        $blocks = $this->_getBlocks();
        $data = [];
        foreach ($blocks as $blockName => $blockInstance) {
            $data[$blockName] = $blockInstance->toHtml();
        }

        $this->getResponse()->setPrivateHeaders(\Magento\PageCache\Helper\Data::PRIVATE_MAX_AGE_CACHE);
        $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     * Returns block content as part of ESI request from Varnish
     */
    public function esiAction()
    {
        $response = $this->getResponse();
        $blocks = $this->_getBlocks();
        $html = '';
        $ttl = 0;

        foreach ($blocks as $blockInstance) {
            $html = $blockInstance->toHtml();
            $ttl = $blockInstance->getTtl();
        }

        $response->appendBody($html);
        $response->setPublicHeaders($ttl);
    }

    /**
     * @return array $data['output', 'ttl']
     */
    protected function _getBlocks()
    {
        $blocks = json_decode($this->getRequest()->getParam('blocks', []));
        $handles = json_decode($this->getRequest()->getParam('handles', []));

        if (!$handles || !$blocks) {
            return [];
        }

        $this->_view->loadLayout($handles);
        $data = array();

        $layout = $this->_view->getLayout();
        foreach ($blocks as $blockName) {
            $blockInstance = $layout->getBlock($blockName);
            if (is_object($blockInstance)) {
                $data[] = $blockInstance;
            }
        }

        return $data;
    }
}
