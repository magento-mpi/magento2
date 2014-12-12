<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Gift Wrapping Controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Giftwrapping extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init active menu
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_GiftWrapping::sales_magento_giftwrapping');

        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Gift Wrapping'));
        return $this;
    }

    /**
     * Init model
     *
     * @param string $requestParam
     * @return \Magento\GiftWrapping\Model\Wrapping
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _initModel($requestParam = 'id')
    {
        $model = $this->_coreRegistry->registry('current_giftwrapping_model');
        if ($model) {
            return $model;
        }
        $model = $this->_objectManager->create('Magento\GiftWrapping\Model\Wrapping');
        $model->setStoreId($this->getRequest()->getParam('store', 0));

        $wrappingId = $this->getRequest()->getParam($requestParam);
        if ($wrappingId) {
            $model->load($wrappingId);
            if (!$model->getId()) {
                throw new \Magento\Framework\Model\Exception(__('Please request the correct gift wrapping.'));
            }
        }
        $this->_coreRegistry->register('current_giftwrapping_model', $model);

        return $model;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GiftWrapping::magento_giftwrapping');
    }

    /**
     * Prepare Gift Wrapping Raw data
     *
     * @param array $wrappingRawData
     * @return array
     */
    protected function _prepareGiftWrappingRawData($wrappingRawData)
    {
        if (isset($wrappingRawData['tmp_image'])) {
            $wrappingRawData['tmp_image'] = basename($wrappingRawData['tmp_image']);
        }
        if (isset($wrappingRawData['image_name']['value'])) {
            $wrappingRawData['image_name']['value'] = basename($wrappingRawData['image_name']['value']);
        }
        return $wrappingRawData;
    }
}
