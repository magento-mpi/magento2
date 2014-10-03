<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Controller\Ajax;

class Suggest extends \Magento\Framework\App\Action\Action
{
    /**
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            $this->getResponse()->setRedirect($this->_url->getBaseUrl());
        }

        $suggestData = $this->_objectManager->get('Magento\CatalogSearch\Helper\Data')->getSuggestData();
        $this->getResponse()->representJson(json_encode($suggestData));
    }
}
