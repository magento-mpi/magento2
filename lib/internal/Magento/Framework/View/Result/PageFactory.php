<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Result;

use Magento\Framework\ObjectManager;

/**
 * A factory that knows how to create a "page" result
 * Requires an instance of controller action in order to impose page type,
 * which is by convention is determined from the controller action class
 */
class PageFactory
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create new page regarding its type
     *
     * TODO: As argument has to be controller action interface, temporary solution until controller output models
     * TODO: are not implemented
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function create()
    {
        return $this->objectManager->create('\Magento\Framework\View\Result\Page');
    }
}
