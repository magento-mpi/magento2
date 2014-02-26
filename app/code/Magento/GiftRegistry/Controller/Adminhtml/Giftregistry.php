<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Core\Exception;

class Giftregistry extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init active menu and set breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_GiftRegistry::customer_magento_giftregistry')
            ->_addBreadcrumb(
                __('Gift Registry'),
                __('Gift Registry')
            );

        $this->_title->add(__('Gift Registry Types'));
        return $this;
    }

    /**
     * Initialize model
     *
     * @param string $requestParam
     * @return \Magento\GiftRegistry\Model\Type
     * @throws Exception
     */
    protected function _initType($requestParam = 'id')
    {
        $type = $this->_objectManager->create('Magento\GiftRegistry\Model\Type');
        $type->setStoreId($this->getRequest()->getParam('store', 0));

        $typeId = $this->getRequest()->getParam($requestParam);
        if ($typeId) {
            $type->load($typeId);
            if (!$type->getId()) {
                throw new Exception(__('Please correct the  gift registry ID.'));
            }
        }
        $this->_coreRegistry->register('current_giftregistry_type', $type);
        return $type;
    }

    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_view->renderLayout();
    }

    /**
     * Create new gift registry type
     *
     * @return void
     */
    public function newAction()
    {
        try {
            $model = $this->_initType();
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/');
            return;
        }

        $this->_initAction();
        $this->_title->add(__('New Gift Registry Type'));

        $block = $this->_view->getLayout()->createBlock('Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit')
            ->setData('form_action_url', $this->getUrl('adminhtml/*/save'));

        $this->_addBreadcrumb(__('New Type'), __('New Type'))
            ->_addContent($block)
            ->_addLeft($this->_view->getLayout()->createBlock(
                'Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tabs')
            );
        $this->_view->renderLayout();
    }

    /**
     * Edit gift registry type
     *
     * @return void
     */
    public function editAction()
    {
        try {
            $model = $this->_initType();
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/');
            return;
        }

        $this->_initAction();
        $this->_title->add(__('%1', $model->getLabel()));

        $block = $this->_view->getLayout()->createBlock('Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit')
            ->setData('form_action_url', $this->getUrl('adminhtml/*/save'));

        $this->_addBreadcrumb(__('Edit Type'), __('Edit Type'))
            ->_addContent($block)
            ->_addLeft(
                $this->_view->getLayout()->createBlock('Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tabs')
            );
        $this->_view->renderLayout();
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        /* @var $filterManager \Magento\Filter\FilterManager */
        $filterManager = $this->_objectManager->get('Magento\Filter\FilterManager');
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
    public function saveAction()
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
                    $this->_redirect('adminhtml/*/edit', array('id' => $model->getId(), 'store' => $model->getStoreId()));
                    return;
                }
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We couldn't save this gift registry type."));
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/');
    }

    /**
     * Delete gift registry type
     *
     * @return void
     */
    public function deleteAction()
    {
        try {
            $model = $this->_initType();
            $model->delete();
            $this->messageManager->addSuccess(__('You deleted the gift registry type.'));
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__("We couldn't delete this gift registry type."));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->_redirect('adminhtml/*/');
    }

    /**
     * Check the permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GiftRegistry::customer_magento_giftregistry');
    }
}
