<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Controller\Varien\Action;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\App\FrontController
     */
    protected $_frontController = null;

    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Should inherited page be rendered
     *
     * @var bool
     */
    protected $_isRenderInherited;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\FrontController $frontController
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param boolean $isRenderInherited
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\App\RequestInterface $request,
        \Magento\App\ResponseInterface $response,
        \Magento\ObjectManager $objectManager,
        \Magento\App\FrontController $frontController,
        \Magento\Core\Model\Layout $layout,
        \Magento\Event\ManagerInterface $eventManager,
        $isRenderInherited
    ) {
        $this->_request           = $request;
        $this->_response          = $response;
        $this->_objectManager     = $objectManager;
        $this->_frontController   = $frontController;
        $this->_layout            = $layout;
        $this->_eventManager      = $eventManager;
        $this->_isRenderInherited = $isRenderInherited;
        $this->_logger            = $logger;
    }

    /**
     * Should inherited page be rendered
     *
     * @return bool
     */
    public function isRenderInherited()
    {
        return $this->_isRenderInherited;
    }

    /**
     * @return \Magento\App\FrontController
     */
    public function getFrontController()
    {
        return $this->_frontController;
    }

    /**
     * @return \Magento\Core\Model\Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @return \Magento\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * @return \Magento\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return \Magento\App\ResponseInterface
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return \Magento\Event\ManagerInterface
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento\Core\Model\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
