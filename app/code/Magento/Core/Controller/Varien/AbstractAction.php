<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract redirect/forward action class
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Controller\Varien;

abstract class AbstractAction
    implements \Magento\Core\Controller\Varien\DispatchableInterface
{
    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Core\Controller\Response\Http
     */
    protected $_response;

    /**
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\Core\Controller\Response\Http $response
     */
    public function __construct(
        \Magento\Core\Controller\Request\Http $request,
        \Magento\Core\Controller\Response\Http $response
    ) {
        $this->_request  = $request;
        $this->_response = $response;
    }

    /**
     * Retrieve request object
     *
     * @return \Magento\Core\Controller\Request\Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve response object
     *
     * @return \Magento\Core\Controller\Response\Http
     */
    public function getResponse()
    {
        if (!$this->_response->getHeader('X-Frame-Options')) {
            $this->_response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        }
        return $this->_response;
    }

    /**
     * Retrieve full bane of current action current controller and
     * current module
     *
     * @param   string $delimiter
     * @return  string
     */
    public function getFullActionName($delimiter = '_')
    {
        return $this->getRequest()->getRequestedRouteName() . $delimiter .
            $this->getRequest()->getRequestedControllerName() . $delimiter .
            $this->getRequest()->getRequestedActionName();
    }
}
