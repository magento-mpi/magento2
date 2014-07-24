<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Result;

use Magento\Framework\ObjectManager;
use Magento\Framework\App\RequestInterface;

/**
 * A factory that knows how to create a "page" result
 * Requires an instance of controller action in order to impose page type,
 * which is by convention is determined from the controller action class
 */
class PageFactory
{
    private $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\View\Result\Page
     */
    public function create(RequestInterface $request)
    {
        $pageType = strtolower($request->getFullActionName());
        return $this->objectManager->create('\Magento\Framework\View\Result\Page', ['pageType' => $pageType]);
    }
}
