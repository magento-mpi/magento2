<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Control;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class Container
 */
class Container extends AbstractBlock
{
    /**
     * Default button class
     */
    const DEFAULT_BUTTON = 'Magento\Ui\Component\Control\Button';

    /**
     * Create button renderer
     *
     * @param string $blockName
     * @param string $blockClassName
     * @return \Magento\Backend\Block\Widget\Button
     */
    protected function createButton($blockName, $blockClassName = null)
    {
        if (null === $blockClassName) {
            $blockClassName = static::DEFAULT_BUTTON;
        }

        return $this->getLayout()->createBlock($blockClassName, $blockName);
    }

    /**
     * Render element HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var \Magento\Ui\Component\Control\Item $item */
        $item = $this->getButtonItem();
        $data = $item->getData();

        $block = $this->createButton(
            $this->getData('context')->getNameInLayout() . '-' . $item->getId() . '-button',
            isset($data['class_name']) ? $data['class_name'] : null
        );
        $block->setData($data);

        return $block->toHtml();
    }
}
