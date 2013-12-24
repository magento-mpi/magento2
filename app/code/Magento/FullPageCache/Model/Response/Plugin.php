<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model\Response;
use Magento\App\RequestInterface;
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
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @param Helper\Url $urlHelper
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param RequestInterface $request
     */
    public function __construct(
        Helper\Url $urlHelper,
        \Magento\Message\ManagerInterface $messageManager,
        RequestInterface $request
    ) {
        $this->_urlHelper = $urlHelper;
        $this->messageManager = $messageManager;
        $this->_request = $request;
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
        $httpHost = $this->_request->getHttpHost();
        $urlHost = parse_url($url, PHP_URL_HOST);
        if ($httpHost != $urlHost && $this->messageManager->getMessages()->getCount() > 0) {
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
