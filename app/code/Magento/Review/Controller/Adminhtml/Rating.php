<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Admin ratings controller
 */
class Rating extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->_initEnityId();
        $this->_view->loadLayout();

        $this->_setActiveMenu('Magento_Review::catalog_reviews_ratings_ratings');
        $this->_addBreadcrumb(__('Manage Ratings'), __('Manage Ratings'));

        $this->_view->renderLayout();
    }

    /**
     * @return void
     */
    public function editAction()
    {
        $this->_initEnityId();
        $this->_view->loadLayout();

        $ratingModel = $this->_objectManager->create('Magento\Review\Model\Rating');
        if ($this->getRequest()->getParam('id')) {
            $ratingModel->load($this->getRequest()->getParam('id'));
        }

        $this->_title->add($ratingModel->getId() ? $ratingModel->getRatingCode() : __('New Rating'));

        $this->_setActiveMenu('Magento_Review::catalog_reviews_ratings_ratings');
        $this->_addBreadcrumb(__('Manage Ratings'), __('Manage Ratings'));

        $this->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Review\Block\Adminhtml\Rating\Edit')
        )->_addLeft(
            $this->_view->getLayout()->createBlock('Magento\Review\Block\Adminhtml\Rating\Edit\Tabs')
        );
        $this->_view->renderLayout();
    }

    /**
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save rating
     *
     * @return void
     */
    public function saveAction()
    {
        $this->_initEnityId();

        if ($this->getRequest()->getPost()) {
            try {
                $ratingModel = $this->_objectManager->create('Magento\Review\Model\Rating');

                $stores = $this->getRequest()->getParam('stores');
                $position = (int)$this->getRequest()->getParam('position');
                $stores[] = 0;
                $isActive = (bool)$this->getRequest()->getParam('is_active');
                $ratingModel->setRatingCode(
                    $this->getRequest()->getParam('rating_code')
                )->setRatingCodes(
                    $this->getRequest()->getParam('rating_codes')
                )->setStores(
                    $stores
                )->setPosition(
                    $position
                )->setId(
                    $this->getRequest()->getParam('id')
                )->setIsActive(
                    $isActive
                )->setEntityId(
                    $this->_coreRegistry->registry('entityId')
                )->save();

                $options = $this->getRequest()->getParam('option_title');

                if (is_array($options)) {
                    $i = 1;
                    foreach ($options as $key => $optionCode) {
                        $optionModel = $this->_objectManager->create('Magento\Review\Model\Rating\Option');
                        if (!preg_match("/^add_([0-9]*?)$/", $key)) {
                            $optionModel->setId($key);
                        }

                        $optionModel->setCode(
                            $optionCode
                        )->setValue(
                            $i
                        )->setRatingId(
                            $ratingModel->getId()
                        )->setPosition(
                            $i
                        )->save();
                        $i++;
                    }
                }

                $this->messageManager->addSuccess(__('You saved the rating.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setRatingData(false);

                $this->_redirect('review/rating/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get(
                    'Magento\Backend\Model\Session'
                )->setRatingData(
                    $this->getRequest()->getPost()
                );
                $this->_redirect('review/rating/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('review/rating/');
    }

    /**
     * @return void
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = $this->_objectManager->create('Magento\Review\Model\Rating');
                /* @var $model \Magento\Review\Model\Rating */
                $model->load($this->getRequest()->getParam('id'))->delete();
                $this->messageManager->addSuccess(__('You deleted the rating.'));
                $this->_redirect('review/rating/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('review/rating/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('review/rating/');
    }

    /**
     * @return void
     */
    protected function _initEnityId()
    {
        $this->_title->add(__('Ratings'));

        $this->_coreRegistry->register(
            'entityId',
            $this->_objectManager->create('Magento\Review\Model\Rating\Entity')->getIdByCode('product')
        );
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Review::ratings');
    }
}
