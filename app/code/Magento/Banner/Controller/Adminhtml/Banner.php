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

class Banner extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
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
        $this->_title(__('Banners'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Banner::cms_magento_banner');
        $this->renderLayout();
    }

    /**
     * Create new banner
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit action
     *
     */
    public function editAction()
    {
        $bannerId = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $bannerId) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')
                ->addError(__('This banner no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($model->getId() ? $model->getName() : __('New Banner'));

        $data = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Banner::cms_magento_banner');
        $this->_addBreadcrumb(
            $bannerId ? __('Edit Banner') : __('New Banner'),
            $bannerId ? __('Edit Banner') : __('New Banner')
        )
        ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        $data = $this->getRequest()->getPost();
        if ($data) {

            $bannerId = $this->getRequest()->getParam('id');
            $model = $this->_initBanner();
            if (!$model->getId() && $bannerId) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
                    __('This banner does not exist.')
                );
                $this->_redirect('*/*/');
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
                $related = $this->_objectManager->get('Magento\Adminhtml\Helper\Js')
                    ->decodeGridSerializedInput($data['banner_catalog_rules']);
                foreach ($related as $_key => $_rid) {
                    $related[$_key] = (int)$_rid;
                }
                $data['banner_catalog_rules'] = $related;
            }
            if (isset($data['banner_sales_rules'])) {
                $related = $this->_objectManager->get('Magento\Adminhtml\Helper\Js')
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
                    \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData($data);
                }
                $model->save();
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData(false);
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(
                    __('You saved the banner.')
                );
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (\Exception $e) {
                $this->_getSession()->addError(
                    __('We cannot save the banner.')
                );
                $redirectBack = true;
                $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
            }
            if ($redirectBack) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     *
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
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(
                    __('The banner has been deleted.')
                );
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addError(
                // @codingStandardsIgnoreStart
                    __('Something went wrong deleting banner data. Please review the action log and try again.')
                // @codingStandardsIgnoreEnd
                );
                $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
                // save data in session
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData($this->getRequest()->getParams());
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $bannerId));
                return;
            }
        }
        // display error message
        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
            __('We cannot find a banner to delete.')
        );
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Delete specified banners using grid massaction
     *
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('banner');
        if (!is_array($ids)) {
            $this->_getSession()->addError(__('Please select a banner(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = \Mage::getSingleton('Magento\Banner\Model\Banner')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    __('You deleted %1 record(s).', count($ids))
                );
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addError(
                // @codingStandardsIgnoreStart
                    __('Something went wrong mass-deleting banners. Please review the action log and try again.')
                // @codingStandardsIgnoreEnd
                );
                $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }


    /**
     * Load Banner from request
     *
     * @param string $idFieldName
     * @return \Magento\Banner\Model\Banner $model
     */
    protected function _initBanner($idFieldName = 'banner_id')
    {
        $this->_title(__('Banners'));

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
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Banner sales rule grid action on promotions tab
     * Load banner by ID from post data
     * Register banner model
     *
     */
    public function salesRuleGridAction()
    {
        $bannerId = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $bannerId) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
                __('This banner does not exist.')
            );
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();
        $this->getLayout()
            ->getBlock('banner_salesrule_grid')
            ->setSelectedSalesRules($this->getRequest()->getPost('selected_salesrules'));
        $this->renderLayout();
    }

    /**
     * Banner catalog rule grid action on promotions tab
     * Load banner by ID from post data
     * Register banner model
     *
     */
    public function catalogRuleGridAction()
    {
        $bannerId = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $bannerId) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
                __('This banner does not exist.')
            );
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();
        $this->getLayout()
            ->getBlock('banner_catalogrule_grid')
            ->setSelectedCatalogRules($this->getRequest()->getPost('selected_catalogrules'));
        $this->renderLayout();
    }

    /**
     * Banner binding tab grid action on sales rule
     *
     */
    public function salesRuleBannersGridAction()
    {
        $ruleId = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magento\SalesRule\Model\Rule');

        if ($ruleId) {
            $model->load($ruleId);
            if (! $model->getRuleId()) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
                    __('This rule no longer exists.')
                );
                $this->_redirect('*/*');
                return;
            }
        }
        if (!$this->_registry->registry('current_promo_quote_rule')) {
            $this->_registry->register('current_promo_quote_rule', $model);
        }
        $this->loadLayout();
        $this->getLayout()
            ->getBlock('related_salesrule_banners_grid')
            ->setSelectedSalesruleBanners($this->getRequest()->getPost('selected_salesrule_banners'));
        $this->renderLayout();
    }

    /**
     * Banner binding tab grid action on catalog rule
     *
     */
    public function catalogRuleBannersGridAction()
    {
        $ruleId = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magento\CatalogRule\Model\Rule');

        if ($ruleId) {
            $model->load($ruleId);
            if (! $model->getRuleId()) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
                    __('This rule no longer exists.')
                );
                $this->_redirect('*/*');
                return;
            }
        }
        if (!$this->_registry->registry('current_promo_catalog_rule')) {
            $this->_registry->register('current_promo_catalog_rule', $model);
        }
        $this->loadLayout();
        $this->getLayout()
            ->getBlock('related_catalogrule_banners_grid')
            ->setSelectedCatalogruleBanners($this->getRequest()->getPost('selected_catalogrule_banners'));
        $this->renderLayout();
    }
}
