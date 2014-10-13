<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\View\Result;

use Magento\Framework\App;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Backend\App\AbstractAction;

class Redirect extends \Magento\Framework\Controller\Result\Redirect
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * @param App\Response\RedirectInterface $redirect
     * @param UrlInterface $backendUrl
     * @param Session $session
     * @param ActionFlag $actionFlag
     */
    public function __construct(
        App\Response\RedirectInterface $redirect,
        UrlInterface $backendUrl,
        Session $session,
        ActionFlag $actionFlag
    ) {
        parent::__construct($redirect);
        $this->backendUrl = $backendUrl;
        $this->session = $session;
        $this->actionFlag = $actionFlag;
    }

    /**
     * {@inheritdoc}
     */
    protected function render(App\ResponseInterface $response)
    {
        $this->session->setIsUrlNotice($this->actionFlag->get('', AbstractAction::FLAG_IS_URLS_CHECKED));
        $response->setRedirect($this->backendUrl->getUrl($this->url, $this->arguments));
        return $this;
    }
}
