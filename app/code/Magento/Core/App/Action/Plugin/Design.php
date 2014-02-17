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
     * @param array $arguments
     * @param \Magento\Interception\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch(array $arguments, \Magento\Interception\InvocationChain $invocationChain)
    {
        $this->_designLoader->load();
        return $invocationChain->proceed($arguments);
    }
}
