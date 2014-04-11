<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\App\Request;

class PathInfoProcessor implements \Magento\App\Request\PathInfoProcessorInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
    }

    /**
     * Process path info
     *
     * @param \Magento\App\RequestInterface $request
     * @param string $pathInfo
     * @return string
     */
    public function process(\Magento\App\RequestInterface $request, $pathInfo)
    {
        $pathParts = explode('/', ltrim($pathInfo, '/'), 2);
        $storeCode = $pathParts[0];

        $stores = $this->_storeManager->getStores(false, true);
        if (isset($stores[$storeCode]) && $stores[$storeCode]->isUseStoreInUrl()) {
            if (!$request->isDirectAccessFrontendName($storeCode)) {
                $this->_storeManager->setCurrentStore($storeCode);
                $pathInfo = '/' . (isset($pathParts[1]) ? $pathParts[1] : '');
                return $pathInfo;
            } elseif (!empty($storeCode)) {
                $request->setActionName('noroute');
                return $pathInfo;
            }
            return $pathInfo;
        }
        return $pathInfo;
    }
}
