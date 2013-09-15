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
 * Admin ratings controller
 */

namespace Magento\Adminhtml\Controller;

class Rating extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    public function indexAction()
    {
        $this->_initEnityId();
        $this->loadLayout();

        $this->_setActiveMenu('Magento_Review::catalog_reviews_ratings_ratings');
        $this->_addBreadcrumb(__('Manage Ratings'), __('Manage Ratings'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_initEnityId();
        $this->loadLayout();

        $ratingModel = \Mage::getModel('Magento\Rating\Model\Rating');
        if ($this->getRequest()->getParam('id')) {
            $ratingModel->load($this->getRequest()->getParam('id'));
        }

        $this->_title($ratingModel->getId() ? $ratingModel->getRatingCode() : __('New Rating'));

        $this->_setActiveMenu('Magento_Review::catalog_reviews_ratings_ratings');
        $this->_addBreadcrumb(__('Manage Ratings'), __('Manage Ratings'));

        $this->_addContent($this->getLayout()->createBlock('Magento\Adminhtml\Block\Rating\Edit'))
            ->_addLeft($this->getLayout()->createBlock('Magento\Adminhtml\Block\Rating\Edit\Tabs'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save rating
     */
    public function saveAction()
    {
        $this->_initEnityId();

        if ($this->getRequest()->getPost()) {
            try {
                $ratingModel = \Mage::getModel('Magento\Rating\Model\Rating');

                $stores = $this->getRequest()->getParam('stores');
                $position = (int)$this->getRequest()->getParam('position');
                $stores[] = 0;
                $isActive = (bool)$this->getRequest()->getParam('is_active');
                $ratingModel->setRatingCode($this->getRequest()->getParam('rating_code'))
                    ->setRatingCodes($this->getRequest()->getParam('rating_codes'))
                    ->setStores($stores)
                    ->setPosition($position)
                    ->setId($this->getRequest()->getParam('id'))
                    ->setIsActive($isActive)
                    ->setEntityId($this->_coreRegistry->registry('entityId'))
                    ->save();

                $options = $this->getRequest()->getParam('option_title');

                if (is_array($options)) {
                    $i = 1;
                    foreach ($options as $key => $optionCode) {
                        $optionModel = \Mage::getModel('Magento\Rating\Model\Rating\Option');
                        if (!preg_match("/^add_([0-9]*?)$/", $key)) {
                            $optionModel->setId($key);
                        }

                        $optionModel->setCode($optionCode)
                            ->setValue($i)
                            ->setRatingId($ratingModel->getId())
                            ->setPosition($i)
                            ->save();
                        $i++;
                    }
                }

                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('You saved the rating.'));
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setRatingData(false);

                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setRatingData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = \Mage::getModel('Magento\Rating\Model\Rating');
                /* @var $model \Magento\Rating\Model\Rating */
                $model->load($this->getRequest()->getParam('id'))
                    ->delete();
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('You deleted the rating.'));
                $this->_redirect('*/*/');
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    protected function _initEnityId()
    {
        $this->_title(__('Ratings'));

        $this->_coreRegistry->register(
            'entityId', \Mage::getModel('Magento\Rating\Model\Rating\Entity')->getIdByCode('product')
        );
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Rating::ratings');
    }
}
