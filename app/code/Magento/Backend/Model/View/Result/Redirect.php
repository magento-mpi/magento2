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
     * Constructor
     *
     * @param App\Response\RedirectInterface $redirect
     * @param UrlInterface $urlBuilder
     * @param Session $session
     * @param ActionFlag $actionFlag
     */
    public function __construct(
        App\Response\RedirectInterface $redirect,
        UrlInterface $urlBuilder,
        Session $session,
        ActionFlag $actionFlag
    ) {
        $this->session = $session;
        $this->actionFlag = $actionFlag;
        parent::__construct($redirect, $urlBuilder);
    }

    /**
     * {@inheritdoc}
     */
    protected function render(App\ResponseInterface $response)
    {
        $this->session->setIsUrlNotice($this->actionFlag->get('', AbstractAction::FLAG_IS_URLS_CHECKED));
        return parent::render($response);
    }
}
