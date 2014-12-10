<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Controller\Adminhtml\Giftregistry;

use Magento\Framework\Model\Exception;

class Save extends \Magento\GiftRegistry\Controller\Adminhtml\Giftregistry
{
    /**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        /* @var $filterManager \Magento\Framework\Filter\FilterManager */
        $filterManager = $this->_objectManager->get('Magento\Framework\Filter\FilterManager');
        if (!empty($data['type']['label'])) {
            $data['type']['label'] = $filterManager->stripTags($data['type']['label']);
        }
        if (!empty($data['attributes']['registry'])) {
            foreach ($data['attributes']['registry'] as &$regItem) {
                if (!empty($regItem['label'])) {
                    $regItem['label'] = $filterManager->stripTags($regItem['label']);
                }
                if (!empty($regItem['options'])) {
                    foreach ($regItem['options'] as &$option) {
                        $option['label'] = $filterManager->stripTags($option['label']);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Save gift registry type
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            //filtering
            $data = $this->_filterPostData($data);
            try {
                $model = $this->_initType();
                $model->loadPost($data);
                $model->save();
                $this->messageManager->addSuccess(__('You saved the gift registry type.'));

                $redirectBack = $this->getRequest()->getParam('back', false);
                if ($redirectBack) {
                    $this->_redirect(
                        'adminhtml/*/edit',
                        ['id' => $model->getId(), 'store' => $model->getStoreId()]
                    );
                    return;
                }
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', ['id' => $model->getId()]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We couldn't save this gift registry type."));
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/');
    }
}
