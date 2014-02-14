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

        if (!empty($blocks)){
            $blockInstance = array_shift($blocks);
            $html = $blockInstance->toHtml();
            $ttl = $blockInstance->getTtl();
        }

        $response->appendBody($html);
        $response->setPublicHeaders($ttl);
    }

    /**
     * Get blocks from layout by handles
     *
     * @return array [\Element\BlockInterface]
     */
    protected function _getBlocks()
    {
        $blocks = $this->getRequest()->getParam('blocks', '');
        $handles = $this->getRequest()->getParam('handles', '');

        if (!$handles || !$blocks) {
            return [];
        }
        $blocks = json_decode($blocks);
        $handles = json_decode($handles);

        $this->_view->loadLayout($handles);
        $data = array();

        $layout = $this->_view->getLayout();
        foreach ($blocks as $blockName) {
            $blockInstance = $layout->getBlock($blockName);
            if (is_object($blockInstance)) {
                $data[$blockName] = $blockInstance;
            }
        }

        return $data;
    }
}
