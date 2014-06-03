<?php
/**
 * Abstract application router
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Router;

use Magento\Framework\App\ActionFactory;

abstract class AbstractRouter implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\FrontController
     */
    protected $_front;

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $_actionFactory;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     */
    public function __construct(ActionFactory $actionFactory)
    {
        $this->_actionFactory = $actionFactory;
    }
}
