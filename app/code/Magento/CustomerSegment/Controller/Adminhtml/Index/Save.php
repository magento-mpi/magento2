<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

class Save extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Save customer segment
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            try {
                $redirectBack = $this->getRequest()->getParam('back', false);

                $model = $this->_initSegment('segment_id');

                // Sanitize apply_to property
                if (array_key_exists('apply_to', $data)) {
                    $data['apply_to'] = (int)$data['apply_to'];
                }

                $validateResult = $model->validateData(new \Magento\Framework\Object($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                    $this->_getSession()->setFormData($data);

                    $this->_redirect('customersegment/*/edit', ['id' => $model->getId()]);
                    return;
                }

                if (array_key_exists('rule', $data)) {
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }

                $model->loadPost($data);
                $this->_session->setPageData($model->getData());
                $model->save();
                if ($model->getApplyTo() != \Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS) {
                    $model->matchCustomers();
                }

                $this->messageManager->addSuccess(__('You saved the segment.'));
                $this->_session->setPageData(false);

                if ($redirectBack) {
                    $this->_redirect('customersegment/*/edit', ['id' => $model->getId(), '_current' => true]);
                    return;
                }
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setPageData($data);
                $this->_redirect('customersegment/*/edit', ['id' => $this->getRequest()->getParam('segment_id')]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We're unable to save the segment."));
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
        }
        $this->_redirect('customersegment/*/');
    }
}
