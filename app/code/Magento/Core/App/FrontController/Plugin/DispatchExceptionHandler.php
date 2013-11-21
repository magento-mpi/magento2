<?php
/**
 * Dispatch exception handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\FrontController\Plugin;

use Magento\Core\Model\StoreManager,
    Magento\App\Dir;

class DispatchExceptionHandler
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * @var \Magento\App\Dir
     */
    protected $_dir;

    /**
     * @param StoreManager $storeManager
     * @param Dir $dir
     */
    public function __construct(
        StoreManager $storeManager,
        Dir $dir
    ) {
        $this->_storeManager = $storeManager;
        $this->_dir = $dir;
    }

    /**
     * Handle dispatch exceptions
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        try {
            return $invocationChain->proceed($arguments);
        } catch (\Magento\Core\Model\Session\Exception $e) {
            header('Location: ' . $this->_storeManager->getStore()->getBaseUrl());
            exit;
        } catch (\Magento\Core\Model\Store\Exception $e) {
            require $this->_dir->getDir(Dir::PUB) . DS . 'errors' . DS . '404.php';
            exit;
        }
    }
}
