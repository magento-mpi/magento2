<?php
/**
 * Dispatch exception handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\App\FrontController\Plugin;

use Magento\Store\Model\StoreManager;
use Magento\App\Filesystem;

class DispatchExceptionHandler
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
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
    public function __construct(StoreManager $storeManager, Filesystem $filesystem)
    {
        $this->_storeManager = $storeManager;
        $this->filesystem = $filesystem;
    }

    /**
     * Handle dispatch exceptions
     *
     * @param \Magento\App\FrontController $subject
     * @param callable $proceed
     * @param \Magento\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\App\FrontController $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        try {
            return $proceed($request);
        } catch (\Magento\Session\Exception $e) {
            header('Location: ' . $this->_storeManager->getStore()->getBaseUrl());
            exit;
        } catch (\Magento\Store\Model\Exception $e) {
            require $this->filesystem->getPath(Filesystem::PUB_DIR) . '/errors/404.php';
            exit;
        }
    }
}
