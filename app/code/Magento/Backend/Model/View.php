<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model;

class View extends \Magento\App\View
{
    /**
     * @var \Magento\Core\Model\Layout\Filter\Acl
     */
    protected $_aclFilter;

    /**
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\Translate $translator
     * @param \Magento\App\ActionFlag $actionFlag
     * @param \Magento\Core\Model\Layout\Filter\Acl $aclFilter
     */
    public function __construct(
        \Magento\View\LayoutInterface $layout,
        \Magento\App\RequestInterface $request,
        \Magento\App\ResponseInterface $response,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\Translate $translator,
        \Magento\App\ActionFlag $actionFlag,
        \Magento\Core\Model\Layout\Filter\Acl $aclFilter
    ) {
        $this->_aclFilter = $aclFilter;
        parent::__construct($layout, $request, $response, $configScope, $eventManager, $translator, $actionFlag);
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

} 
