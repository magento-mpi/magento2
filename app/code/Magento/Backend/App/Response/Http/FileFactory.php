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
     * @var \Magento\App\ActionFlag
     */
    protected $_flag;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\App\ResponseFactory $responseFactory
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Backend\Model\Url $backendUrl
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\App\ActionFlag $flag
     * @param \Magento\Backend\Helper\Data $helper
     */
    public function __construct(
        \Magento\App\ResponseFactory $responseFactory,
        \Magento\Filesystem\Driver\File $filesystemDriver,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Model\Url $backendUrl,
        \Magento\Backend\Model\Session $session,
        \Magento\App\ActionFlag $flag,
        \Magento\Backend\Helper\Data $helper
    ) {
        $this->_auth = $auth;
        $this->_backendUrl = $backendUrl;
        $this->_session = $session;
        $this->_flag = $flag;
        $this->_helper = $helper;
        parent::__construct($responseFactory, $filesystemDriver);
    }


    /**
     * Set redirect into response
     *
     * @param   string $path
     * @param   array $arguments
     * @return \Magento\App\ResponseInterface
     * @TODO move method
     */
    protected function _redirect($path, $arguments=array())
    {
        $this->_session
            ->setIsUrlNotice($this->_flag->get('', \Magento\Backend\App\AbstractAction::FLAG_IS_URLS_CHECKED));
        $response = $this->_responseFactory->create();
        $response->setRedirect($this->_helper->getUrl($path, $arguments));
        return $response;
    }

    /**
     * Declare headers and content file in response for file download
     *
     * @param string $fileName
     * @param string|array $content set to null to avoid starting output, $contentLength should be set explicitly in
     * that case
     * @param string $contentType
     * @param int $contentLength    explicit content length, if strlen($content) isn't applicable
     * @return \Magento\App\ResponseInterface
     */
    public function create($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null)
    {
        if ($this->_auth->getAuthStorage()->isFirstPageAfterLogin()) {
            $response = $this->_redirect($this->_backendUrl->getStartupPageUrl());
            return $response;
        }
        return parent::create($fileName, $content, $contentType, $contentLength);
    }
}