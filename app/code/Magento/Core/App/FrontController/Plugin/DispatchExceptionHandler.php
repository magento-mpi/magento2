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
    Magento\App\Filesystem;

class DispatchExceptionHandler
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Filesystem instance
     *
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @param StoreManager $storeManager
     * @param Filesystem $filesystem
     */
    public function __construct(
        StoreManager $storeManager,
        Filesystem $filesystem
    ) {
        $this->_storeManager = $storeManager;
        $this->filesystem = $filesystem;
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
        } catch (\Magento\Session\Exception $e) {
            header('Location: ' . $this->_storeManager->getStore()->getBaseUrl());
            exit;
        } catch (\Magento\Core\Model\Store\Exception $e) {
            require $this->filesystem->getPath(Filesystem::PUB) . '/errors/404.php';
            exit;
        }
    }
}
