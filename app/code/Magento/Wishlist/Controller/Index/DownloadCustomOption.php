<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Wishlist\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Wishlist\Controller\IndexInterface;
use Magento\Framework\App\Action;

class DownloadCustomOption extends Action\Action implements IndexInterface
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileResponseFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileResponseFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileResponseFactory
    ) {
        $this->_fileResponseFactory = $fileResponseFactory;
        parent::__construct($context);
    }

    /**
     * Custom options download action
     *
     * @return void
     */
    public function execute()
    {
        $option = $this->_objectManager->create(
            'Magento\Wishlist\Model\Item\Option'
        )->load(
            $this->getRequest()->getParam('id')
        );

        if (!$option->getId()) {
            return $this->_forward('noroute');
        }

        $optionId = null;
        if (strpos($option->getCode(), \Magento\Catalog\Model\Product\Type\AbstractType::OPTION_PREFIX) === 0) {
            $optionId = str_replace(
                \Magento\Catalog\Model\Product\Type\AbstractType::OPTION_PREFIX,
                '',
                $option->getCode()
            );
            if ((int)$optionId != $optionId) {
                return $this->_forward('noroute');
            }
        }
        $productOption = $this->_objectManager->create('Magento\Catalog\Model\Product\Option')->load($optionId);

        if (!$productOption ||
            !$productOption->getId() ||
            $productOption->getProductId() != $option->getProductId() ||
            $productOption->getType() != 'file'
        ) {
            return $this->_forward('noroute');
        }

        try {
            $info = unserialize($option->getValue());
            $filePath = $this->_objectManager->get(
                'Magento\Framework\App\Filesystem'
            )->getPath(
                DirectoryList::ROOT
            ) . $info['quote_path'];
            $secretKey = $this->getRequest()->getParam('key');

            if ($secretKey == $info['secret_key']) {
                $this->_fileResponseFactory->create(
                    $info['title'],
                    array('value' => $filePath, 'type' => 'filename'),
                    DirectoryList::ROOT
                );
            }
        } catch (\Exception $e) {
            $this->_forward('noroute');
        }
        exit(0);
    }
}
