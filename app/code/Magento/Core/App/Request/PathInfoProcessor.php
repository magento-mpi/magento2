<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\Request;

class PathInfoProcessor implements \Magento\App\Request\PathInfoProcessorInterface
{
    /**
     * @var \Magento\Core\Model\StoreManager
     */
    private $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManager $storeManager
     */
    public function __construct(\Magento\Core\Model\StoreManager $storeManager)
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

        $stores = $this->_storeManager->getStores(true, true);
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