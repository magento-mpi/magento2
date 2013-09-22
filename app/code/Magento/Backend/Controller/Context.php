<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Controller context
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
namespace Magento\Backend\Controller;

class Context extends \Magento\Core\Controller\Varien\Action\Context
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_translator;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Controller_Request_Http $request
     * @param \Magento\Core\Controller\Response\Http $response
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Controller\Varien\Front $frontController
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\AuthorizationInterface $authorization
     * @param \Magento\Core\Model\Translate $translator
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        \Magento\Core\Controller\Request\Http $request,
        \Magento\Core\Controller\Response\Http $response,
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Controller\Varien\Front $frontController,
        \Magento\Core\Model\Layout $layout,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Backend\Model\Session $session,
        \Magento\Backend\Helper\Data $helper,
        \Magento\AuthorizationInterface $authorization,
        \Magento\Core\Model\Translate $translator
    ) {
        parent::__construct($logger, $request, $response, $objectManager, $frontController, $layout, $eventManager);
        $this->_session = $session;
        $this->_helper = $helper;
        $this->_authorization = $authorization;
        $this->_translator = $translator;
    }

    /**
     * @return \Magento\Backend\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return \Magento\Backend\Model\Session
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * @return \Magento\AuthorizationInterface
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }

    /**
     * @return \Magento\Core\Model\Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }
}
