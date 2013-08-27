<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Quick style CSS renderer
 */
class Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer
{
    /**
     * Quick style renderer factory
     *
     * @var Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Factory
     */
    protected $_quickStyleFactory;

    /**
     * @param Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Factory $factory
     */
    public function __construct(Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Factory $factory)
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
     * @return Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer
     */
    protected function _rendererCssRecursively($data, &$content)
    {
        if (isset($data['components'])) {
            foreach ($data['components'] as $component) {
                $this->_rendererCssRecursively($component, $content);
            }
        } elseif ((!empty($data['value']) && $data['value'] != $data['default'] && !empty($data['attribute'])) ||
                (empty($data['value']) && $this->_isBackgroundImage($data))) {
            $content .= $this->_quickStyleFactory->get($data['attribute'])->toCss($data) . "\n";
        }
        return $this;
    }

    /**
     * Override the parent's default value for this specific component.
     *
     * @param array $data
     * @return bool
     */
    protected function _isBackgroundImage($data)
    {
        return (!empty($data['attribute']) && $data['attribute'] === 'background-image' &&
            !empty($data['type']) && $data['type'] === 'image-uploader' &&
            !empty($data['selector']) && $data['selector'] === '.header');
    }
}
