<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Result;

use Magento\Framework\ObjectManager;

class LayoutFactory
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
    public function __construct(ObjectManager $objectManager, $instanceName = 'Magento\Framework\View\Result\Layout')
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function create()
    {
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->objectManager->create($this->instanceName);
        $resultLayout->addDefaultHandle();
        return $resultLayout;
    }
}
