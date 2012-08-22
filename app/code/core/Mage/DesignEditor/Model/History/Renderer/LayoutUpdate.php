<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * History output renderer to get layout update
 */
class Mage_DesignEditor_Model_History_Renderer_LayoutUpdate implements Mage_DesignEditor_Model_History_RendererInterface
{
    /**
     * Get Layout update out of collection of changes
     *
     * @param Mage_DesignEditor_Model_Change_Collection $collection
     * @return string
     */
    public function render(Mage_DesignEditor_Model_Change_Collection $collection)
    {
        $layoutUpdate = '';
        foreach ($collection as $item) {
            if ($item instanceof Mage_DesignEditor_Model_Change_Layout)
                $layoutUpdate .= $this->_render($item);
        }

        return $layoutUpdate;
    }

    /**
     * Render layout update for single layout change
     *
     * @param Mage_DesignEditor_Model_Change_Layout $item
     * @throws Magento_Exception
     * @return string
     */
    protected function _render($item)
    {
        switch ($item->getData('action_name')) {
            case Mage_DesignEditor_Model_Change_Layout::LAYOUT_DIRECTIVE_REMOVE:
                $xml = sprintf('<remove name="%s" />', $item->getData('element_name'));
                break;

            case Mage_DesignEditor_Model_Change_Layout::LAYOUT_DIRECTIVE_MOVE:
                $xml = sprintf('<move element="%s" destination="%s" />',
                    $item->getData('element_name'),
                    $item->getData('destination')
                );
                break;

            default:
                throw new Magento_Exception('Invalid layout directive');
        }
        return $xml;
    }
}
