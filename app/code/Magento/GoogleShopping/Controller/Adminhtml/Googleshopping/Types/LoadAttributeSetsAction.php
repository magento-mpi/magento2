<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Controller\Adminhtml\Googleshopping\Types;

class LoadAttributeSetsAction extends \Magento\GoogleShopping\Controller\Adminhtml\Googleshopping\Types
{
    /**
     * Get available attribute sets
     *
     * @return void
     */
    protected function execute()
    {
        try {
            $this->getResponse()->setBody(
                $this->_view->getLayout()->getBlockSingleton(
                    'Magento\GoogleShopping\Block\Adminhtml\Types\Edit\Form'
                )->getAttributeSetsSelectElement(
                    $this->getRequest()->getParam('target_country')
                )->toHtml()
            );
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            // just need to output text with error
            $this->messageManager->addError(__("We can't load attribute sets."));
        }
    }
}
