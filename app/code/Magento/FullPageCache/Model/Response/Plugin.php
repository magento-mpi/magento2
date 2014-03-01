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
     * @param \Magento\App\Response\Http $subject
     * @param string $url
     * @param int $code
     *
     * @return void|array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetRedirect(\Magento\App\Response\Http $subject, $url, $code = 302)
    {
        if (!$url) {
            return;
        }

        $httpHost = $this->_request->getHttpHost();
        $urlHost = parse_url($url, PHP_URL_HOST);
        if ($httpHost != $urlHost && $this->messageManager->getMessages()->getCount() > 0) {
            $url = $this->_urlHelper->addRequestParam(
                $url,
                array(
                    \Magento\FullPageCache\Model\Cache::REQUEST_MESSAGE_GET_PARAM => null
                )
            );
        }
        return array('url' => $url, 'code' => $code);
    }
}
