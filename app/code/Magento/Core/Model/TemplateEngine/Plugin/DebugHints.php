<?php
/**
 * Plugin for the template engine factory that makes a decision of whether to activate debugging hints or not
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\TemplateEngine\Plugin;

class DebugHints
{
    /**#@+
     * XPath of configuration of the debugging hints
     */
    const XML_PATH_DEBUG_TEMPLATE_HINTS         = 'dev/debug/template_hints';
    const XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS  = 'dev/debug/template_hints_blocks';
    /**#@-*/

    /**
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    private $_storeConfig;

    /**
     * @var \Magento\Core\Helper\Data
     */
    private $_coreData;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Core\Helper\Data $coreData
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Core\Helper\Data $coreData
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeConfig = $storeConfig;
        $this->_coreData = $coreData;
    }

    /**
     * Wrap template engine instance with the debugging hints decorator, depending of the store configuration
     *
     * @param \Magento\View\TemplateEngineInterface $invocationResult
     * @return \Magento\View\TemplateEngineInterface
     */
    public function afterCreate(\Magento\View\TemplateEngineFactory $subject, \Magento\View\TemplateEngineInterface $invocationResult)
    {
        if ($this->_storeConfig->getConfig(self::XML_PATH_DEBUG_TEMPLATE_HINTS) && $this->_coreData->isDevAllowed()) {
            $showBlockHints = $this->_storeConfig->getConfig(self::XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS);
            return $this->_objectManager->create(
                'Magento\Core\Model\TemplateEngine\Decorator\DebugHints',
                array(
                    'subject' => $invocationResult,
                    'showBlockHints' => $showBlockHints,
                )
            );
        }
        return $invocationResult;
    }
}
