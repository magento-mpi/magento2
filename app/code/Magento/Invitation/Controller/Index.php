<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation frontend controller
 *
 */
namespace Magento\Invitation\Controller;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * Invitation Config
     *
     * @var \Magento\Invitation\Model\Config
     */
    protected $_config;

    /**
     * Invitation Factory
     *
     * @var \Magento\Invitation\Model\InvitationFactory
     */
    protected $invitationFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Invitation\Model\Config $config
     * @param \Magento\Invitation\Model\InvitationFactory $invitationFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Invitation\Model\Config $config,
        \Magento\Invitation\Model\InvitationFactory $invitationFactory
    ) {
        parent::__construct($context);
        $this->_session = $session;
        $this->_config = $config;
        $this->invitationFactory = $invitationFactory;
    }

    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_config->isEnabledOnFront()) {
            throw new NotFoundException();
        }

        if (!$this->_session->authenticate($this)) {
            $this->getResponse()->setRedirect(
                $this->_objectManager->get('Magento\Customer\Model\Url')->getLoginUrl()
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }
}
