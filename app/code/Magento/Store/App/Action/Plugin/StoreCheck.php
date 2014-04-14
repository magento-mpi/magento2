<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\App\Action\Plugin;

class StoreCheck
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\App\State $appState
    ) {
        $this->_storeManager = $storeManager;
        $this->_appState = $appState;
    }

    /**
     * @param \Magento\App\Action\Action $subject
     * @param callable $proceed
     * @param \Magento\App\RequestInterface $request
     *
     * @return \Magento\App\ResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Magento\Store\Model\Exception
     */
    public function aroundDispatch(
        \Magento\App\Action\Action $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        if ($this->_appState->isInstalled()) {
            if (!$this->_storeManager->getStore()->getIsActive()) {
                throw new \Magento\Store\Model\Exception(
                    'Current store is not active.'
                );
            }
        }
        return $proceed($request);
    }
}
