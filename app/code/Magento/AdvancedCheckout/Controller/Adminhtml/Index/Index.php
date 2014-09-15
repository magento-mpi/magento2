<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

use Magento\Framework\Model\Exception;

class Index extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    /**
     * Manage shopping cart layout
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }
            $this->_view->loadLayout();
            $this->_initTitle();
            $this->_view->renderLayout();
            return;
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->messageManager->addError(__('An error has occurred. See error log for details.'));
        }
        $this->_redirect('checkout/*/error');
    }
}
