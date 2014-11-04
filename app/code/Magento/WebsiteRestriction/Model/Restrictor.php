<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model;

class Restrictor
{
    /**
     * @var \Magento\WebsiteRestriction\Model\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerHelper;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $_session;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @param ConfigInterface $config
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Customer\Model\Url $customerUrl
     */
    public function __construct(
        \Magento\WebsiteRestriction\Model\ConfigInterface $config,
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        $this->customerUrl = $customerUrl;
        $this->_config = $config;
        $this->_customerHelper = $customerHelper;
        $this->_session = $session;
        $this->_scopeConfig = $scopeConfig;
        $this->_url = $urlFactory;
        $this->_actionFlag = $actionFlag;
    }

    /**
     * Restrict access to website
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param bool $isCustomerLoggedIn
     * @return void
     */
    public function restrict($request, $response, $isCustomerLoggedIn)
    {
        switch ($this->_config->getMode()) {
            // show only landing page with 503 or 200 code
            case \Magento\WebsiteRestriction\Model\Mode::ALLOW_NONE:
                if ($request->getFullActionName() !== 'restriction_index_stub') {
                    $request->setModuleName(
                        'restriction'
                    )->setControllerName(
                        'index'
                    )->setActionName(
                        'stub'
                    )->setDispatched(
                        false
                    );
                    return;
                }
                $httpStatus = $this->_config->getHTTPStatusCode();
                if (\Magento\WebsiteRestriction\Model\Mode::HTTP_503 === $httpStatus) {
                    $response->setHeader('HTTP/1.1', '503 Service Unavailable');
                }
                break;

            case \Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER:
                // break intentionally omitted

                //redirect to landing page/login
            case \Magento\WebsiteRestriction\Model\Mode::ALLOW_LOGIN:
                if (!$isCustomerLoggedIn && !$this->_customerHelper->isLoggedIn()) {
                    // see whether redirect is required and where
                    $redirectUrl = false;
                    $allowedActionNames = $this->_config->getGenericActions();
                    if ($this->_customerHelper->isRegistrationAllowed()) {
                        $allowedActionNames = array_merge($allowedActionNames, $this->_config->getRegisterActions());
                    }

                    // to specified landing page
                    $restrictionRedirectCode = $this->_config->getHTTPRedirectCode();
                    if (\Magento\WebsiteRestriction\Model\Mode::HTTP_302_LANDING === $restrictionRedirectCode) {
                        $cmsPageViewAction = 'cms_page_view';
                        $allowedActionNames[] = $cmsPageViewAction;
                        $pageIdentifier = $this->_config->getLandingPageCode();
                        // Restrict access to CMS pages too
                        if (!in_array($request->getFullActionName(), $allowedActionNames)
                            || $request->getFullActionName() === $cmsPageViewAction
                            && $request->getAlias('rewrite_request_path') !== $pageIdentifier
                        ) {
                            $redirectUrl = $this->_url->getUrl('', array('_direct' => $pageIdentifier));
                        }
                    } elseif (!in_array($request->getFullActionName(), $allowedActionNames)) {
                        // to login form
                        $redirectUrl = $this->_url->getUrl('customer/account/login');
                    }

                    if ($redirectUrl) {
                        $response->setRedirect($redirectUrl);
                        $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                    }
                    $redirectToDashboard = $this->_scopeConfig->isSetFlag(
                        \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    if ($redirectToDashboard) {
                        $afterLoginUrl = $this->customerUrl->getDashboardUrl();
                    } else {
                        $afterLoginUrl = $this->_url->getUrl();
                    }
                    $this->_session->setWebsiteRestrictionAfterLoginUrl($afterLoginUrl);
                } elseif ($this->_session->hasWebsiteRestrictionAfterLoginUrl()) {
                    $response->setRedirect($this->_session->getWebsiteRestrictionAfterLoginUrl(true));
                    $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                }
                break;
        }

    }
}
