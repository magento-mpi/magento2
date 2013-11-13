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
     * @var \Magento\Core\Model\DesignLoader
     */
    protected $_designLoader;

    /**
     * @param \Magento\Core\Model\DesignLoader $designLoader
     */
    public function __construct(\Magento\Core\Model\DesignLoader $designLoader)
    {
        $this->_designLoader = $designLoader;
    }

    /**
     * Initialize design
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $this->_designLoader->load();
        return $invocationChain->proceed($arguments);
    }
}
