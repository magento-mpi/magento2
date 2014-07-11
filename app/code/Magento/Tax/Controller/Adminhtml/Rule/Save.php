<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rule;

use \Magento\Backend\App\Action;

class Save extends \Magento\Tax\Controller\Adminhtml\Rule
{
    /**
     * @return void
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPost();
        if ($postData) {

            $ruleModel = $this->_objectManager->get('Magento\Tax\Model\Calculation\Rule');
            $ruleModel->setData($postData);
            $ruleModel->setCalculateSubtotal($this->getRequest()->getParam('calculate_subtotal', 0));

            try {
                $ruleModel->save();

                $this->messageManager->addSuccess(__('The tax rule has been saved.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('tax/*/edit', array('rule' => $ruleModel->getId()));
                    return;
                }

                $this->_redirect('tax/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong saving this tax rule.'));
            }

            $this->_objectManager->get('Magento\Backend\Model\Session')->setRuleData($postData);
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
            return;
        }
        $this->getResponse()->setRedirect($this->getUrl('tax/rule'));
    }
}
