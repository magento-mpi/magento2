<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\View\Result;

use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ActionFlag;
use Magento\Backend\App\AbstractAction;

class Forward extends \Magento\Framework\Controller\Result\Forward
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
     * @param RequestInterface $request
     * @param Session $session
     * @param ActionFlag $actionFlag
     */
    public function __construct(RequestInterface $request, Session $session, ActionFlag $actionFlag)
    {
        $this->session = $session;
        $this->actionFlag = $actionFlag;
        parent::__construct($request);
    }

    /**
     * @param string $action
     * @return $this
     */
    public function forward($action)
    {
        $this->session->setIsUrlNotice($this->actionFlag->get('', AbstractAction::FLAG_IS_URLS_CHECKED));
        return parent::forward($action);
    }
}
