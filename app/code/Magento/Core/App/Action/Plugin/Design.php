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
     * @return array
     */
    public function beforeDispatch(array $arguments = array())
    {
        $this->_designLoader->load();
        return $arguments;
    }
}
