<?php
/**
 * Abstract application router
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Router;

use \Magento\App\ActionFactory;

abstract class AbstractRouter implements \Magento\App\RouterInterface
{
    /**
     * @var \Magento\App\FrontController
     */
    protected $_front;

    /**
     * @var \Magento\App\ActionFactory
     */
    protected $_actionFactory;

    /**
     * @param \Magento\App\ActionFactory $actionFactory
     */
    public function __construct(ActionFactory $actionFactory)
    {
        $this->_actionFactory = $actionFactory;
    }
}
