<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Controller\Ajax;

class Logout extends Login
{
    /**
     * Customer logout action
     *
     * @return void
     */
    public function execute()
    {
        $lastCustomerId = $this->customerSession->getId();
        $this->customerSession->logout()->setBeforeAuthUrl(
            $this->_redirect->getRefererUrl()
        )->setLastCustomerId(
            $lastCustomerId
        );

        $this->getResponse()->representJson($this->helper->jsonEncode(['message' => 'Logout Successful']));
    }
}

