<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\App\Action\Plugin;


class StoreCheck
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    /**
     * Dispatch request
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch(\Magento\App\Action\Action $subject, \Closure $proceed, \Magento\App\RequestInterface $request)
    {
        if (!$this->_storeManager->getStore()->getIsActive())
        {
            $this->_storeManager->throwStoreException();
        }
        return $invocationChain->proceed($arguments);
    }
} 
