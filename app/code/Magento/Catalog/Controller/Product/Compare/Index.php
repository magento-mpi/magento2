<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Product\Compare;

class Index extends \Magento\Catalog\Controller\Product\Compare
{
    /**
     * Compare index action
     *
     * @return void
     */
    public function execute()
    {
        $items = $this->getRequest()->getParam('items');

        $beforeUrl = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED);
        if ($beforeUrl) {
            $this->_catalogSession->setBeforeCompareUrl(
                $this->_objectManager->get('Magento\Core\Helper\Data')->urlDecode($beforeUrl)
            );
        }

        if ($items) {
            $items = explode(',', $items);
            /** @var \Magento\Catalog\Model\Product\Compare\ListCompare $list */
            $list = $this->_catalogProductCompareList;
            $list->addProducts($items);
            $this->_redirect('*/*/*');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
