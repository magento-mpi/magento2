<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\View\Page;

use Magento\Framework\App;
use Magento\Framework\View;
use Magento\Framework\Event;
use Magento\Backend\Model\View\Layout;

class Builder extends View\Page\Builder
{
    /**
     * @var Layout\Filter\Acl $aclFilter
     */
    protected $aclFilter;

    /**
     * @param View\LayoutInterface $layout
     * @param App\Request\Http $request
     * @param Event\ManagerInterface $eventManager
     * @param View\Page\Config $pageConfig
     * @param View\Page\Layout\Reader $pageLayoutReader
     * @param Layout\Filter\Acl $aclFilter
     */
    public function __construct(
        View\LayoutInterface $layout,
        App\Request\Http $request,
        Event\ManagerInterface $eventManager,
        View\Page\Config $pageConfig,
        View\Page\Layout\Reader $pageLayoutReader,
        Layout\Filter\Acl $aclFilter
    ) {
        parent::__construct($layout, $request, $eventManager, $pageConfig, $pageLayoutReader);
        $this->aclFilter = $aclFilter;
    }

    /**
     * @return $this
     */
    protected function beforeGenerateBlock()
    {
        $this->aclFilter->filterAclNodes($this->layout->getNode());
        return $this;
    }

    /**
     * @return $this
     */
    protected function afterGenerateBlock()
    {
        $this->layout->initMessages();
        return $this;
    }
}
