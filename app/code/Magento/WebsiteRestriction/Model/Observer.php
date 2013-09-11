<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Private sales and stubs observer
 *
 */
namespace Magento\WebsiteRestriction\Model;

class Observer
{
    /**
     * Implement website stub or private sales restriction
     *
     * @param \Magento\Event\Observer $observer
     */
    public function restrictWebsite($observer)
    {
        /* @var $controller \Magento\Core\Controller\Front\Action */
        $controller = $observer->getEvent()->getControllerAction();

        if (!\Mage::app()->getStore()->isAdmin()) {
            $dispatchResult = new \Magento\Object(array('should_proceed' => true, 'customer_logged_in' => false));
            \Mage::dispatchEvent('websiterestriction_frontend', array(
                'controller' => $controller, 'result' => $dispatchResult
            ));
            if (!$dispatchResult->getShouldProceed()) {
                return;
            }
            if (!\Mage::helper('Magento\WebsiteRestriction\Helper\Data')->getIsRestrictionEnabled()) {
                return;
            }
            /* @var $request \Magento\Core\Controller\Request\Http */
            $request    = $controller->getRequest();
            /* @var $response \Magento\Core\Controller\Response\Http */
            $response   = $controller->getResponse();
            switch ((int)\Mage::getStoreConfig(\Magento\WebsiteRestriction\Helper\Data::XML_PATH_RESTRICTION_MODE)) {
                // show only landing page with 503 or 200 code
                case \Magento\WebsiteRestriction\Model\Mode::ALLOW_NONE:
                    if ($controller->getFullActionName() !== 'restriction_index_stub') {
                        $request->setModuleName('restriction')
                            ->setControllerName('index')
                            ->setActionName('stub')
                            ->setDispatched(false);
                        return;
                    }
                    $httpStatus = (int)\Mage::getStoreConfig(
                        \Magento\WebsiteRestriction\Helper\Data::XML_PATH_RESTRICTION_HTTP_STATUS
                    );
                    if (\Magento\WebsiteRestriction\Model\Mode::HTTP_503 === $httpStatus) {
                        $response->setHeader('HTTP/1.1','503 Service Unavailable');
                    }
                    break;

                case \Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER:
                    // break intentionally omitted

                // redirect to landing page/login
                case \Magento\WebsiteRestriction\Model\Mode::ALLOW_LOGIN:
                    if (!$dispatchResult->getCustomerLoggedIn() && !\Mage::helper('Magento\Customer\Helper\Data')->isLoggedIn()) {
                        // see whether redirect is required and where
                        $redirectUrl = false;
                        $allowedActionNames = array_keys(\Mage::getConfig()
                            ->getNode(\Magento\WebsiteRestriction\Helper\Data::XML_NODE_RESTRICTION_ALLOWED_GENERIC)
                            ->asArray()
                        );
                        if (\Mage::helper('Magento\Customer\Helper\Data')->isRegistrationAllowed()) {
                            foreach(array_keys(\Mage::getConfig()
                                ->getNode(
                                    \Magento\WebsiteRestriction\Helper\Data::XML_NODE_RESTRICTION_ALLOWED_REGISTER
                                )
                                ->asArray()) as $fullActionName
                            ) {
                                $allowedActionNames[] = $fullActionName;
                            }
                        }

                        // to specified landing page
                        $restrictionRedirectCode = (int)\Mage::getStoreConfig(
                            \Magento\WebsiteRestriction\Helper\Data::XML_PATH_RESTRICTION_HTTP_REDIRECT
                        );
                        if (\Magento\WebsiteRestriction\Model\Mode::HTTP_302_LANDING === $restrictionRedirectCode) {
                            $cmsPageViewAction = 'cms_page_view';
                            $allowedActionNames[] = $cmsPageViewAction;
                            $pageIdentifier = \Mage::getStoreConfig(
                                \Magento\WebsiteRestriction\Helper\Data::XML_PATH_RESTRICTION_LANDING_PAGE
                            );
                            // Restrict access to CMS pages too
                            if (!in_array($controller->getFullActionName(), $allowedActionNames)
                                || ($controller->getFullActionName() === $cmsPageViewAction
                                    && $request->getAlias('rewrite_request_path') !== $pageIdentifier)
                            ) {
                                $redirectUrl = \Mage::getUrl('', array('_direct' => $pageIdentifier));
                            }
                        } elseif (!in_array($controller->getFullActionName(), $allowedActionNames)) {
                            // to login form
                            $redirectUrl = \Mage::getUrl('customer/account/login');
                        }

                        if ($redirectUrl) {
                            $response->setRedirect($redirectUrl);
                            $controller->setFlag('', \Magento\Core\Controller\Varien\Action::FLAG_NO_DISPATCH, true);
                        }
                        if (\Mage::getStoreConfigFlag(
                            \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                        )) {
                            $afterLoginUrl = \Mage::helper('Magento\Customer\Helper\Data')->getDashboardUrl();
                        } else {
                            $afterLoginUrl = \Mage::getUrl();
                        }
                        \Mage::getSingleton('Magento\Core\Model\Session')->setWebsiteRestrictionAfterLoginUrl($afterLoginUrl);
                    } elseif (\Mage::getSingleton('Magento\Core\Model\Session')->hasWebsiteRestrictionAfterLoginUrl()) {
                        $response->setRedirect(
                            \Mage::getSingleton('Magento\Core\Model\Session')->getWebsiteRestrictionAfterLoginUrl(true)
                        );
                        $controller->setFlag('', \Magento\Core\Controller\Varien\Action::FLAG_NO_DISPATCH, true);
                    }
                    break;
            }
        }
    }

    /**
     * Attempt to disallow customers registration
     *
     * @param \Magento\Event\Observer $observer
     */
    public function restrictCustomersRegistration($observer)
    {
        $result = $observer->getEvent()->getResult();
        if ((!\Mage::app()->getStore()->isAdmin()) && $result->getIsAllowed()) {
            $restrictionMode = (int)\Mage::getStoreConfig(
                \Magento\WebsiteRestriction\Helper\Data::XML_PATH_RESTRICTION_MODE
            );
            $result->setIsAllowed((!\Mage::helper('Magento\WebsiteRestriction\Helper\Data')->getIsRestrictionEnabled())
                || (\Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER === $restrictionMode)
            );
        }
    }

    /**
     * Make layout load additional handler when in private sales mode
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addPrivateSalesLayoutUpdate($observer)
    {
        if (in_array((int)\Mage::getStoreConfig(\Magento\WebsiteRestriction\Helper\Data::XML_PATH_RESTRICTION_MODE),
            array(
                \Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER,
                \Magento\WebsiteRestriction\Model\Mode::ALLOW_LOGIN
            ),
            true
        )) {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle('restriction_privatesales_mode');
        }
    }
}
