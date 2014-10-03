<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Generator;

use Magento\Framework\View\Layout;

class UiComponent extends Block
{
    const TYPE = 'ui_component';

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * Creates UI Component object based on xml node data and add it to the layout
     *
     * @param \Magento\Framework\View\Layout\Reader\Context $readerContext
     * @param string $elementName
     * @return $this
     * @throws \Magento\Framework\Exception
     */
    public function process(Layout\Reader\Context $readerContext, $elementName)
    {
        $scheduledStructure = $readerContext->getScheduledStructure();
        $structure = $readerContext->getStructure();

        // TODO: Eliminate $node, move it to reader object
        list(, $node, $actions, $args) = $this->_scheduledStructure->getElement($elementName);
        $configPath = (string)$node->getAttribute('ifconfig');
        if (!empty($configPath)
            && !$this->scopeConfig->isSetFlag($configPath, $this->scopeType, $this->scopeResolver->getScope())
        ) {
            $scheduledStructure->unsetElement($elementName);
            return;
        }

        $group = (string)$node->getAttribute('group');
        if (!empty($group)) {
            $structure->addToParentGroup($elementName, $group);
        }

        $arguments = $this->_evaluateArguments($args);

        // create Ui Component Object
        $componentName = (string)$node['component'];

        $uiComponent = $this->_uiComponentFactory->createUiComponent($componentName, $elementName, $arguments);

        $this->_blocks[$elementName] = $uiComponent;

        return $uiComponent;
    }
}