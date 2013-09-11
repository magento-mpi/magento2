<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml entity sets controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Controller\Catalog\Product;

class Set extends \Magento\Adminhtml\Controller\Action
{
    public function indexAction()
    {
        $this->_title(__('Product Templates'));

        $this->_setTypeId();

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Catalog::catalog_attributes_sets');

        $this->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_addBreadcrumb(
            __('Manage Attribute Sets'),
            __('Manage Attribute Sets'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title(__('Product Templates'));

        $this->_setTypeId();
        $attributeSet = \Mage::getModel('Magento\Eav\Model\Entity\Attribute\Set')
            ->load($this->getRequest()->getParam('id'));

        if (!$attributeSet->getId()) {
            $this->_redirect('*/*/index');
            return;
        }

        $this->_title($attributeSet->getId() ? $attributeSet->getAttributeSetName() : __('New Set'));

        \Mage::register('current_attribute_set', $attributeSet);

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Catalog::catalog_attributes_sets');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_addBreadcrumb(
            __('Manage Product Sets'),
            __('Manage Product Sets'));

        $this->_addContent($this->getLayout()->createBlock('Magento\Adminhtml\Block\Catalog\Product\Attribute\Set\Main'));

        $this->renderLayout();
    }

    public function setGridAction()
    {

       $this->_setTypeId();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Save attribute set action
     *
     * [POST] Create attribute set from another set and redirect to edit page
     * [AJAX] Save attribute set data
     *
     */
    public function saveAction()
    {
        $entityTypeId   = $this->_getEntityTypeId();
        $hasError       = false;
        $attributeSetId = $this->getRequest()->getParam('id', false);
        $isNewSet       = $this->getRequest()->getParam('gotoEdit', false) == '1';

        /* @var $model \Magento\Eav\Model\Entity\Attribute\Set */
        $model  = \Mage::getModel('Magento\Eav\Model\Entity\Attribute\Set')
            ->setEntityTypeId($entityTypeId);

        /** @var $helper \Magento\Adminhtml\Helper\Data */
        $helper = \Mage::helper('Magento\Adminhtml\Helper\Data');

        try {
            if ($isNewSet) {
                //filter html tags
                $name = $helper->stripTags($this->getRequest()->getParam('attribute_set_name'));
                $model->setAttributeSetName(trim($name));
            } else {
                if ($attributeSetId) {
                    $model->load($attributeSetId);
                }
                if (!$model->getId()) {
                    \Mage::throwException(__('This attribute set no longer exists.'));
                }
                $data = \Mage::helper('Magento\Core\Helper\Data')->jsonDecode($this->getRequest()->getPost('data'));

                //filter html tags
                $data['attribute_set_name'] = $helper->stripTags($data['attribute_set_name']);

                $model->organizeData($data);
            }

            $model->validate();
            if ($isNewSet) {
                $model->save();
                $model->initFromSkeleton($this->getRequest()->getParam('skeleton_set'));
            }
            $model->save();
            $this->_getSession()->addSuccess(__('You saved the attribute set.'));
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $hasError = true;
        } catch (\Exception $e) {
            $this->_getSession()->addException($e,
                __('An error occurred while saving the attribute set.'));
            $hasError = true;
        }

        if ($isNewSet) {
            if ($this->getRequest()->getPost('return_session_messages_only')) {
                /** @var $block \Magento\Core\Block\Messages */
                $block = $this->_objectManager->get('Magento\Core\Block\Messages');
                $block->setMessages($this->_getSession()->getMessages(true));
                $body = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(array(
                    'messages' => $block->getGroupedHtml(),
                    'error'    => $hasError,
                    'id'       => $model->getId(),
                ));
                $this->getResponse()->setBody($body);
            } else {
                if ($hasError) {
                    $this->_redirect('*/*/add');
                } else {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
            }
        } else {
            $response = array();
            if ($hasError) {
                $this->_initLayoutMessages('Magento\Adminhtml\Model\Session');
                $response['error']   = 1;
                $response['message'] = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
            } else {
                $response['error']   = 0;
                $response['url']     = $this->getUrl('*/*/');
            }
            $this->getResponse()->setBody(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($response));
        }
    }

    public function addAction()
    {
        $this->_title(__('New Product Template'));

        $this->_setTypeId();

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Catalog::catalog_attributes_sets');


        $this->_addContent(
            $this->getLayout()->createBlock('Magento\Adminhtml\Block\Catalog\Product\Attribute\Set\Toolbar\Add')
        );

        $this->renderLayout();
    }

    public function deleteAction()
    {
        $setId = $this->getRequest()->getParam('id');
        try {
            \Mage::getModel('Magento\Eav\Model\Entity\Attribute\Set')
                ->setId($setId)
                ->delete();

            $this->_getSession()->addSuccess(__('The attribute set has been removed.'));
            $this->getResponse()->setRedirect($this->getUrl('*/*/'));
        } catch (\Exception $e) {
            $this->_getSession()->addError(__('An error occurred while deleting this set.'));
            $this->_redirectReferer();
        }
    }

    /**
     * Define in register catalog_product entity type code as entityType
     *
     */
    protected function _setTypeId()
    {
        \Mage::register('entityType',
            \Mage::getModel('Magento\Catalog\Model\Product')->getResource()->getTypeId());
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::sets');
    }

    /**
     * Retrieve catalog product entity type id
     *
     * @return int
     */
    protected function _getEntityTypeId()
    {
        if (is_null(\Mage::registry('entityType'))) {
            $this->_setTypeId();
        }
        return \Mage::registry('entityType');
    }
}
