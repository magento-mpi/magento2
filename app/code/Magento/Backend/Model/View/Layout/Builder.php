<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\View\Layout;

use Magento\Framework\App;
use Magento\Framework\View;
use Magento\Framework\Event;

class Builder extends \Magento\Framework\View\Layout\Builder
{
    /**
     * @var Filter\Acl
     */
    protected $aclFilter;

    /**
     * @param View\LayoutInterface $layout
     * @param App\Request\Http $request
     * @param Event\ManagerInterface $eventManager
     * @param Filter\Acl $aclFilter
     */
    public function __construct(
        View\LayoutInterface $layout,
        App\Request\Http $request,
        Event\ManagerInterface $eventManager,
        Filter\Acl $aclFilter
    ) {
        parent::__construct($layout, $request, $eventManager);
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
