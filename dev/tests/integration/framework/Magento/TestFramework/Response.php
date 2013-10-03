<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * HTTP response implementation that is used instead core one for testing
 */
namespace Magento\TestFramework;

class Response extends \Magento\Core\Controller\Response\Http
{
    /**
     * @inherit
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public $headersSentThrowsException = false;

    /**
     * Prevent generating exceptions if headers are already sent
     *
     * Prevents throwing an exception in \Zend_Controller_Response_Abstract::canSendHeaders()
     * All functionality that depend on headers validation should be covered with unit tests by mocking response.
     *
     * @param bool $throw
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function canSendHeaders($throw = false)
    {
        return true;
    }

    public function sendResponse()
    {
        $this->_eventManager->dispatch('http_response_send_before', array('response'=>$this));
    }
}
