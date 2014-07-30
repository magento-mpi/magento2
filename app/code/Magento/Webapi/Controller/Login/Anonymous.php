<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Login;

use Magento\Authorization\Model\UserContextInterface;

class Anonymous extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $session;

    /**
     * Initialize Login Service
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Session\Generic $session
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Session\Generic $session
    ) {
        parent::__construct($context);
        $this->session = $session;
    }

    /**
     * Initiate a session for unregistered users. Send back the session id.
     *
     * @return void
     */
    public function execute()
    {
        $this->session->start('frontend');
        $this->session->setUserId(0);
        $this->session->setUserType(UserContextInterface::USER_TYPE_GUEST);
        $this->session->regenerateId(true);
    }
}
