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
    private $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Core\Model\StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
    }

    /**
     * Process path info
     *
     * @param string $pathInfo
     * @return string
     */
    public function processStore($pathInfo)
    {
        $pathParts = explode('/', ltrim($pathInfo, '/'), 2);
        $storeCode = $pathParts[0];
        $stores = $this->_storeManager->getStores(false, true);
        if (isset($stores[$storeCode])) {
            $this->_storeManager->setCurrentStore($storeCode);
            $pathInfo = '/' . (isset($pathParts[1]) ? $pathParts[1] : '');
            return $pathInfo;
        } else {
            // store does not exist
            throw new NoSuchEntityException('storeCode', $storeCode);
        }
    }
}
