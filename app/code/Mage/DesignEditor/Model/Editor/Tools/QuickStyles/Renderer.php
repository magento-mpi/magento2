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
 * Quick style CSS renderer
 */
class Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer
{
    /**
     * Quick style renderer factory
     *
     * @var Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Factory
     */
    protected $_quickStyleFactory;

    /**
     * @param Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Factory $factory
     */
    public function __construct(Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Factory $factory)
    {
        $this->_quickStyleFactory = $factory;
    }

    /**
     * Render Quick Style CSS
     *
     * @param array $data
     * @return string
     */
    public function render($data)
    {
        $content = '';
        foreach ($data as $element) {
            $this->_rendererCssRecursively($element, $content);
        }
        return $content;
    }

    /**
     * Render CSS recursively
     *
     * @param array $data
     * @param string $content
     * @return Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer
     */
    protected function _rendererCssRecursively($data, &$content)
    {
        // Override the parent's default value for this specific component.
        $backgroundImageComponent = $data['attribute'] === 'background-image' && $data['type'] === 'image-uploader'
            && $data['selector'] === '.header';

        if (isset($data['components'])) {
            foreach ($data['components'] as $component) {
                $this->_rendererCssRecursively($component, $content);
            }
        } elseif ((!empty($data['value']) && $data['value'] != $data['default'] && !empty($data['attribute'])) ||
                (empty($data['value']) && $backgroundImageComponent)) {
            $content .= $this->_quickStyleFactory->get($data['attribute'])->toCss($data) . "\n";
        }
        return $this;
    }
}
