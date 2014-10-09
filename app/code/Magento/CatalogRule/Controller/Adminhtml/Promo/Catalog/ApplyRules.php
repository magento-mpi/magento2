<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog;

use Magento\CatalogRule\Model\Rule\Job;

class ApplyRules extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog
{

    /**
     * Apply all active catalog price rules
     *
     * @return void
     */
    public function execute()
    {
        $errorMessage = __('Unable to apply rules.');
        try {
            /** @var \Magento\CatalogRule\Model\Indexer\Rule\Action\Full $ruleProcessor */
            $ruleProcessor = $this->_objectManager->get('Magento\CatalogRule\Model\Indexer\Rule\Action\Full');
            $ruleProcessor->execute();
            $this->messageManager->addSuccess(__('The rules will be applied in background'));
            $this->_objectManager->create('Magento\CatalogRule\Model\Flag')->loadSelf()->setState(0)->save();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('catalog_rule/*');
    }
}
