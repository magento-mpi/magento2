<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\App;

use Magento\Catalog\Helper\Data;

/**
 * Class ContextPlugin
 */
class ContextPlugin
{
    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Catalog\Model\Session $session
     * @param \Magento\App\Http\Context $httpContext
     */
    public function __construct(
        \Magento\Catalog\Model\Session $session,
        \Magento\App\Http\Context $httpContext
    ) {
        $this->session = $session;
        $this->httpContext = $httpContext;
    }

    /**
     * Before launch plugin
     *
     * @param \Magento\LauncherInterface $subject
     */
    public function beforeLaunch(\Magento\LauncherInterface $subject)
    {
        if ($this->session->hasSortDirection()) {
            $this->httpContext->setValue(Data::CONTEXT_CATALOG_SORT_DIRECTION, $this->session->getSortDirection());
        }
        if ($this->session->hasSortOrder()) {
            $this->httpContext->setValue(Data::CONTEXT_CATALOG_SORT_ORDER, $this->session->getSortOrder());
        }
        if ($this->session->hasDisplayMode()) {
            $this->httpContext->setValue(Data::CONTEXT_CATALOG_DISPLAY_MODE, $this->session->getDisplayMode());
        }
        if ($this->session->hasLimitPage()) {
            $this->httpContext->setValue(Data::CONTEXT_CATALOG_LIMIT, $this->session->getLimitPage());
        }
    }
}
