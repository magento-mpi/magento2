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
     * @var string
     */
    protected $instanceName;

    /**
     * @param ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(ObjectManager $objectManager, $instanceName = 'Magento\Framework\View\Result\Page')
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create new page regarding its type
     *
     * TODO: As argument has to be controller action interface, temporary solution until controller output models
     * TODO: are not implemented
     *
     * @param bool $isView
     * @return \Magento\Framework\View\Result\Page
     */
    public function create($isView = false)
    {
        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $this->objectManager->create($this->instanceName);
        // TODO Temporary solution for compatibility with View object. Will be deleted in MAGETWO-28359
        if (!$isView) {
            $page->addDefaultHandle();
        }
        return $page;
    }
}
