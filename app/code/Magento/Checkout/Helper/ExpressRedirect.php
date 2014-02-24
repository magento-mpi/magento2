<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Helper;

use Magento\Checkout\Controller\Express\RedirectLoginInterface as RedirectLoginInterface;

class ExpressRedirect extends \Magento\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\App\ActionFlag $actionFlag
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\App\Helper\Context $context
     */
    public function __construct(
        \Magento\App\ActionFlag $actionFlag,
        \Magento\ObjectManager $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\App\Helper\Context $context
    ) {
        $this->_actionFlag = $actionFlag;
        $this->_objectManager = $objectManager;
        $this->_customerSession = $customerSession;

        parent::__construct($context);
    }

    /**
     * Performs redirect to login for checkout
     * @param RedirectLoginInterface $expressRedirect
     * @param string|null $customerBeforeAuthUrlDefault
     */
    public function redirectLogin(RedirectLoginInterface $expressRedirect, $customerBeforeAuthUrlDefault = null)
    {
        $this->_actionFlag->set('', 'no-dispatch', true);
        foreach ($expressRedirect->getActionFlagList() as $actionKey => $actionFlag) {
            $this->_actionFlag->set('', $actionKey, $actionFlag);
        }

        $expressRedirect->getResponse()->setRedirect(
            $this->_objectManager->get('Magento\Core\Helper\Url')->addRequestParam(
                $expressRedirect->getLoginUrl(),
                array('context' => 'checkout')
            )
        );

        $customerBeforeAuthUrl = $customerBeforeAuthUrlDefault;
        if ($expressRedirect->getCustomerBeforeAuthUrl()) {
            $customerBeforeAuthUrl = $expressRedirect->getCustomerBeforeAuthUrl();
        }
        if ($customerBeforeAuthUrl) {
            $this->_customerSession
                ->setBeforeAuthUrl($customerBeforeAuthUrl);
        }
    }
} 