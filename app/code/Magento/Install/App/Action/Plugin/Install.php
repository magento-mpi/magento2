<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\App\Action\Plugin;

class Install
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->_appState = $appState;
        $this->_response = $response;
        $this->_url = $url;
        $this->_actionFlag = $actionFlag;
    }

    /**
     * Dispatch request
     *
     * @param \Magento\Framework\App\Action\Action $subject
     * @param callable $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function aroundDispatch(
        \Magento\Framework\App\Action\Action $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if (!$this->_appState->isInstalled()) {
            $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            $this->_response->setRedirect($this->_url->getUrl('install'));
            return $this->_response;
        }
        return $proceed($request);
    }
}
