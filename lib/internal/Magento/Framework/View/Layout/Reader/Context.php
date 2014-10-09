<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Reader;

use Magento\Framework\View\Layout;

class Context
{
    /**
     * @var \Magento\Framework\View\Layout\ScheduledStructure
     */
    protected $scheduledStructure;

    /**
     * @var \Magento\Framework\Data\Structure
     */
    protected $structure;

    /**
     * @var \Magento\Framework\View\Page\Config\Structure
     */
    protected $pageConfigStructure;

    /**
     * @param Layout\ScheduledStructure $scheduledStructure
     * @param \Magento\Framework\Data\Structure $structure
     * @param \Magento\Framework\View\Page\Config\Structure $pageConfigStructure
     */
    public function __construct(
        Layout\ScheduledStructure $scheduledStructure = null,
        \Magento\Framework\Data\Structure $structure = null,
        \Magento\Framework\View\Page\Config\Structure $pageConfigStructure = null
    ) {
        $this->scheduledStructure = $scheduledStructure;
        $this->structure = $structure;
        $this->pageConfigStructure = $pageConfigStructure;
    }

    /**
     * @return Layout\ScheduledStructure
     */
    public function getScheduledStructure()
    {
        return $this->scheduledStructure;
    }

    /**
     * @return \Magento\Framework\View\Layout\Data\Structure
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @return \Magento\Framework\View\Page\Config\Structure
     */
    public function getPageConfigStructure()
    {
        return $this->pageConfigStructure;
    }
}
