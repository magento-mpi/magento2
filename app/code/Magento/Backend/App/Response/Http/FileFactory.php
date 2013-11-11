<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App\Response\Http;

class FileFactory extends \Magento\App\Response\Http\FileFactory
{
    /**
     * Name of "is URLs checked" flag
     */
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $_backendUrl;

    /**
     * @var \Magento\App\ResponseFactory
     */
    protected $_responseFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Backend\Model\Url $backendUrl
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\App\ResponseFactory $responseFactory
     */
    public function __construct(
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Model\Url $backendUrl,
        \Magento\Backend\Model\Session $session,
        \Magento\Filesystem $filesystem,
        \Magento\App\ResponseFactory $responseFactory
    ) {
        $this->_auth = $auth;
        $this->_backendUrl = $backendUrl;
        $this->_session = $session;
        $this->_responseFactory = $responseFactory;
        parent::__construct($responseFactory, $filesystem);
    }


    /**
     * Set redirect into response
     *
     * @param   string $path
     * @param   array $arguments
     * @return \Magento\Backend\App\AbstractAction
     * @TODO move method
     */
    protected function _redirect($path, $arguments=array())
    {
        $this->_session->setIsUrlNotice($this->getFlag('', self::FLAG_IS_URLS_CHECKED));
        $this->_responseFactory->create()->setRedirect($this->getUrl($path, $arguments));
        return $this;
    }

    /**
     * Declare headers and content file in response for file download
     *
     * @param string $fileName
     * @param string|array $content set to null to avoid starting output, $contentLength should be set explicitly in
     * that case
     * @param string $contentType
     * @param int $contentLength    explicit content length, if strlen($content) isn't applicable
     * @return \Magento\App\ActionInterface
     */
    public function create($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null)
    {
        if ($this->_auth->getAuthStorage()->isFirstPageAfterLogin()) {
            $this->_redirect($this->_backendUrl->getStartupPageUrl());
            return $this;
        }
        return parent::create($fileName, $content, $contentType, $contentLength);
    }
}