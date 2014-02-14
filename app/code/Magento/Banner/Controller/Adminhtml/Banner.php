<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Banner extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $registry
    ) {
        $this->_registry = $registry;
        parent::__construct($context);
    }

    /**
     * Banners list
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Banners'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Banner::cms_magento_banner');
        $this->_view->renderLayout();
    }

    /**
     * Create new banner
     *
     * @return void
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit action
     *
     * @return void
     */
    public function editAction()
    {
        $bannerId = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $bannerId) {
            $this->messageManager->addError(__('This banner no longer exists.'));
            $this->_redirect('adminhtml/*/');
            return;
        }

        $this->_title->add($model->getId() ? $model->getName() : __('New Banner'));

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Banner::cms_magento_banner');
        $this->_addBreadcrumb(
            $bannerId ? __('Edit Banner') : __('New Banner'),
            $bannerId ? __('Edit Banner') : __('New Banner')
        );
        $this->_view->renderLayout();
    }

    /**
     * Save action
     *
     * @return void
     */
    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        $data = $this->getRequest()->getPost();
        if ($data) {

            $bannerId = $this->getRequest()->getParam('id');
            $model = $this->_initBanner();
            if (!$model->getId() && $bannerId) {
                $this->messageManager->addError(
                    __('This banner does not exist.')
                );
                $this->_redirect('adminhtml/*/');
                return;
            }

            //Filter disallowed data
            $currentStores = array_keys($this->_objectManager->get('Magento\Core\Model\StoreManager')->getStores(true));
            if (isset($data['store_contents_not_use'])) {
                $data['store_contents_not_use'] = array_intersect($data['store_contents_not_use'], $currentStores);
            }
            if (isset($data['store_contents'])) {
                $data['store_contents'] = array_intersect_key($data['store_contents'], array_flip($currentStores));
            }

            // prepare post data
            if (isset($data['banner_catalog_rules'])) {
                $related = $this->_objectManager->get('Magento\Backend\Helper\Js')
                    ->decodeGridSerializedInput($data['banner_catalog_rules']);
                foreach ($related as $_key => $_rid) {
                    $related[$_key] = (int)$_rid;
                }
                $data['banner_catalog_rules'] = $related;
            }
            if (isset($data['banner_sales_rules'])) {
                $related = $this->_objectManager->get('Magento\Backend\Helper\Js')
                    ->decodeGridSerializedInput($data['banner_sales_rules']);
                foreach ($related as $_key => $_rid) {
                    $related[$_key] = (int)$_rid;
                }
                $data['banner_sales_rules'] = $related;
            }

            // save model
            try {
                if (!empty($data)) {
                    $model->addData($data);
                    $this->_getSession()->setFormData($data);
                }
                $model->save();
                $this->_getSession()->setFormData(false);
                $this->messageManager->addSuccess(
                    __('You saved the banner.')
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $redirectBack = true;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We cannot save the banner.')
                );
                $redirectBack = true;
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
            if ($redirectBack) {
                $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('adminhtml/*/');
    }

    /**
     * Delete action
     *
     * @return void
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        $bannerId = $this->getRequest()->getParam('id');
        if ($bannerId) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Magento\Banner\Model\Banner');
                $model->load($bannerId);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(
                    __('The banner has been deleted.')
                );
                // go to grid
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                // @codingStandardsIgnoreStart
                    __('Something went wrong deleting banner data. Please review the action log and try again.')
                // @codingStandardsIgnoreEnd
                );
                $this->_objectManager->get('Magento\Logger')->logException($e);
                // save data in session
                $this->_getSession()->setFormData($this->getRequest()->getParams());
                // redirect to edit form
                $this->_redirect('adminhtml/*/edit', array('id' => $bannerId));
                return;
            }
        }
        // display error message
        $this->messageManager->addError(
            __('We cannot find a banner to delete.')
        );
        // go to grid
        $this->_redirect('adminhtml/*/');
    }

    /**
     * Delete specified banners using grid massaction
     *
     * @return void
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('banner');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select a banner(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = $this->_objectManager->create('Magento\Banner\Model\Banner')->load($id);
                    $model->delete();
                }

                $this->messageManager->addSuccess(
                    __('You deleted %1 record(s).', count($ids))
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                // @codingStandardsIgnoreStart
                    __('Something went wrong mass-deleting banners. Please review the action log and try again.')
                // @codingStandardsIgnoreEnd
                );
                $this->_objectManager->get('Magento\Logger')->logException($e);
                return;
            }
        }
        $this->_redirect('adminhtml/*/index');
    }


    /**
     * Load Banner from request
     *
     * @param string $idFieldName
     * @return \Magento\Banner\Model\Banner $model
     */
    protected function _initBanner($idFieldName = 'banner_id')
    {
        $this->_title->add(__('Banners'));

        $bannerId = (int)$this->getRequest()->getParam($idFieldName);
        $model = $this->_objectManager->create('Magento\Banner\Model\Banner');
        if ($bannerId) {
            $model->load($bannerId);
        }
        if (!$this->_registry->registry('current_banner')) {
            $this->_registry->register('current_banner', $model);
        }
        return $model;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Banner::magento_banner');
    }

    /**
     * Render Banner grid
     *
     * @return void
     */
    public function gridAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Banner sales rule grid action on promotions tab
     * Load banner by ID from post data
     * Register banner model
     *
     * @return void
     */
    public function salesRuleGridAction()
    {
        $bannerId = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $bannerId) {
            $this->messageManager->addError(
                __('This banner does not exist.')
            );
            $this->_redirect('adminhtml/*/');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()
            ->getBlock('banner_salesrule_grid')
            ->setSelectedSalesRules($this->getRequest()->getPost('selected_salesrules'));
        $this->_view->renderLayout();
    }

    /**
     * Banner catalog rule grid action on promotions tab
     * Load banner by ID from post data
     * Register banner model
     *
     * @return void
     */
    public function catalogRuleGridAction()
    {
        $bannerId = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $bannerId) {
            $this->messageManager->addError(
                __('This banner does not exist.')
            );
            $this->_redirect('adminhtml/*/');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()
            ->getBlock('banner_catalogrule_grid')
            ->setSelectedCatalogRules($this->getRequest()->getPost('selected_catalogrules'));
        $this->_view->renderLayout();
    }

    /**
     * Banner binding tab grid action on sales rule
     *
     * @return void
     */
    public function salesRuleBannersGridAction()
    {
        $ruleId = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magento\SalesRule\Model\Rule');

        if ($ruleId) {
            $model->load($ruleId);
            if (! $model->getRuleId()) {
                $this->messageManager->addError(
                    __('This rule no longer exists.')
                );
                $this->_redirect('adminhtml/*');
                return;
            }
        }
        if (!$this->_registry->registry('current_promo_quote_rule')) {
            $this->_registry->register('current_promo_quote_rule', $model);
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()
            ->getBlock('related_salesrule_banners_grid')
            ->setSelectedSalesruleBanners($this->getRequest()->getPost('selected_salesrule_banners'));
        $this->_view->renderLayout();
    }

    /**
     * Banner binding tab grid action on catalog rule
     *
     * @return void
     */
    public function catalogRuleBannersGridAction()
    {
        $ruleId = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magento\CatalogRule\Model\Rule');

        if ($ruleId) {
            $model->load($ruleId);
            if (! $model->getRuleId()) {
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('adminhtml/*');
                return;
            }
        }
        if (!$this->_registry->registry('current_promo_catalog_rule')) {
            $this->_registry->register('current_promo_catalog_rule', $model);
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()
            ->getBlock('related_catalogrule_banners_grid')
            ->setSelectedCatalogruleBanners($this->getRequest()->getPost('selected_catalogrule_banners'));
        $this->_view->renderLayout();
    }
}
