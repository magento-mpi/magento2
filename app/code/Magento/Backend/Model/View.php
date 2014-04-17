<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

class View extends \Magento\Framework\App\View
{
    /**
     * @var Layout\Filter\Acl
     */
    protected $_aclFilter;

    /**
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param Layout\Filter\Acl $aclFilter
     */
    public function __construct(
        \Magento\View\LayoutInterface $layout,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Translate\InlineInterface $translateInline,
        \Magento\Framework\App\ActionFlag $actionFlag,
        Layout\Filter\Acl $aclFilter
    ) {
        $this->_aclFilter = $aclFilter;
        parent::__construct($layout, $request, $response, $configScope, $eventManager, $translateInline, $actionFlag);
    }

    /**
     * {@inheritdoc}
     */
    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true)
    {
        parent::loadLayout($handles, false, $generateXml);
        $this->_aclFilter->filterAclNodes($this->getLayout()->getNode());
        if ($generateBlocks) {
            $this->generateLayoutBlocks();
            $this->_isLayoutLoaded = true;
        }
        $this->getLayout()->initMessages();
        return $this;
    }

    /**
     * Returns is layout loaded
     *
     * @return bool
     */
    public function isLayoutLoaded()
    {
        return $this->_isLayoutLoaded;
    }
}
