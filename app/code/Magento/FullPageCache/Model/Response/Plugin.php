<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model\Response;
use \Magento\Core\Model,
    \Magento\Core\Helper;

class Plugin
{
    /**
     * Core url
     *
     * @var \Magento\Core\Helper\Url
     */
    protected $_urlHelper = null;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_coreSession;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_application;

    /**
     * @param Helper\Url $urlHelper
     * @param Model\Session $coreSession
     * @param Model\App $application
     */
    public function __construct(
        Helper\Url $urlHelper,
        Model\Session $coreSession,
        Model\App $application
    ) {
        $this->_urlHelper = $urlHelper;
        $this->_coreSession = $coreSession;
        $this->_application = $application;
   }

    /**
     * Check cross-domain session messages
     *
     * @param array $arguments
     * @return array
     */
    public function beforeSendResponse($arguments)
    {
        $url = isset($arguments[0]) ? $arguments[0] : null;
        if (!$url) {
            return $arguments;
        }
        $httpHost = $this->_application->getFrontController()->getRequest()->getHttpHost();
        $urlHost = parse_url($url, PHP_URL_HOST);
        if ($httpHost != $urlHost && $this->_coreSession->getMessages()->count() > 0) {
            $arguments[0] = $this->_urlHelper->addRequestParam(
                $url,
                array(
                    \Magento\FullPageCache\Model\Cache::REQUEST_MESSAGE_GET_PARAM => null
                )
            );
        }
        return $arguments;
    }
}