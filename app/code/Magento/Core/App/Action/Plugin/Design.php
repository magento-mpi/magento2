<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\App\Action\Plugin;

class Design
{
    /**
     * @var \Magento\View\DesignLoader
     */
    protected $_designLoader;

    /**
     * @param \Magento\View\DesignLoader $designLoader
     */
    public function __construct(\Magento\View\DesignLoader $designLoader)
    {
        $this->_designLoader = $designLoader;
    }

    /**
     * Initialize design
     *
     * @param \Magento\App\Action\Action $subject
     * @param callable $proceed
     * @param \Magento\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\App\Action\Action $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        $this->_designLoader->load();
        return $proceed($request);
    }
}
