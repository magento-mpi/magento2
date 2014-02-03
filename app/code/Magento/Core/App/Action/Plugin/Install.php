<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\App\Action\Plugin;


class Install
{
    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @param \Magento\App\State $appState
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\UrlInterface $url
     * @param \Magento\App\ActionFlag $actionFlag
     */
    public function __construct(
        \Magento\App\State $appState,
        \Magento\App\ResponseInterface $response,
        \Magento\UrlInterface $url,
        \Magento\App\ActionFlag $actionFlag
    ) {
        $this->_appState = $appState;
        $this->_response = $response;
        $this->_url = $url;
        $this->_actionFlag = $actionFlag;
    }

    /**
     * Dispatch request
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\App\ResponseInterface
     */
    public function aroundDispatch(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        if (!$this->_appState->isInstalled()) {
            $this->_actionFlag->set('', \Magento\App\Action\Action::FLAG_NO_DISPATCH, true);
            $this->_response->setRedirect(
                $this->_url->getUrl('install')
            );
            return $this->_response;
        }
        return $invocationChain->proceed($arguments);
    }
}
