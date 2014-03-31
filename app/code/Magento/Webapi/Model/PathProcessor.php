<?php

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model;

use Magento\Exception\NoSuchEntityException;

class PathProcessor
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Core\Model\StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Process path info
     *
     * @param string $path
     * @return string
     * @throws NoSuchEntityException
     */
    public function processStore($path)
    {
        $pathParts = explode('/', ltrim($path, '/'), 2);
        $storeCode = $pathParts[0];
        $stores = $this->storeManager->getStores(false, true);
        if (isset($stores[$storeCode])) {
            $this->storeManager->setCurrentStore($storeCode);
            $path = '/' . (isset($pathParts[1]) ? $pathParts[1] : '');
            return $path;
        } else {
            // store does not exist
            throw new NoSuchEntityException('storeCode', $storeCode);
        }
    }
}
