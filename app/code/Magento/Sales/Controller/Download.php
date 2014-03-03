<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller;

use \Magento\Catalog\Model\Product\Type\AbstractType as AbstractProductType;

/**
 * Sales controller for download purposes
 */
class Download extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\Sales\Model\Download
     */
    protected $_download;

    public function __construct(\Magento\App\Action\Context $context, \Magento\Sales\Model\Download $download)
    {
        $this->_download = $download;
        parent::__construct($context);
    }

    /**
     * Custom options download action
     */
    public function downloadCustomOptionAction()
    {
        $quoteItemOptionId = $this->getRequest()->getParam('id');
        /** @var $option \Magento\Sales\Model\Quote\Item\Option */
        $option = $this->_objectManager->create('Magento\Sales\Model\Quote\Item\Option')->load($quoteItemOptionId);

        if (!$option->getId()) {
            $this->_forward('noroute');
            return;
        }

        $optionId = null;
        if (strpos($option->getCode(), AbstractProductType::OPTION_PREFIX) === 0) {
            $optionId = str_replace(AbstractProductType::OPTION_PREFIX, '', $option->getCode());
            if ((int)$optionId != $optionId) {
                $optionId = null;
            }
        }
        $productOption = null;
        if ($optionId) {
            /** @var $productOption \Magento\Catalog\Model\Product\Option */
            $productOption = $this->_objectManager->create('Magento\Catalog\Model\Product\Option')->load($optionId);
        }
        if (!$productOption || !$productOption->getId()
            || $productOption->getProductId() != $option->getProductId() || $productOption->getType() != 'file'
        ) {
            $this->_forward('noroute');
            return;
        }

        try {
            $info = unserialize($option->getValue());
            if ($this->getRequest()->getParam('key') != $info['secret_key']) {
                $this->_forward('noroute');
                return;
            }
            $this->_download->downloadFile($info);
        } catch (\Exception $e) {
            $this->_forward('noroute');
        }
        exit(0);
    }
}
