<?php
/**
 * Depersonalize customer session data
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Model\Layout;

/**
 * Class DepersonalizePlugin
 */
class DepersonalizePlugin extends \Magento\Customer\Model\Layout\DepersonalizePlugin
{
    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $customerSegmentIds;

    /**
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(
        \Magento\View\LayoutInterface $layout,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\App\RequestInterface $request
    ) {
        $this->layout = $layout;
        $this->customerSession = $customerSession;
        $this->request = $request;
    }

    /**
     * Before layout generate
     *
     * @param mixed $arguments
     * @return mixed
     */
    public function beforeGenerateXml($arguments = null)
    {
        $this->customerSegmentIds = $this->customerSession->getCustomerSegmentIds();
        return $arguments;
    }

    /**
     * After layout generate
     *
     * @param mixed $arguments
     * @return mixed
     */
    public function afterGenerateXml($arguments = null)
    {
        if (!$this->request->isAjax() && $this->layout->isCacheable()) {
            $this->customerSession->setCustomerSegmentIds($this->customerSegmentIds);
        }
        return $arguments;
    }
}
