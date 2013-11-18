<?php
/**
 * Interface for Template Engine
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

interface TemplateEngineInterface
{
    /**
     * Render the named template in the context of a particular block and with
     * the data provided in $vars.
     *
     * @param \Magento\View\Element\BlockInterface $block
     * @param $templateFile
     * @param array $dictionary
     * @return string rendered template
     */
    public function render(\Magento\View\Element\BlockInterface $block, $templateFile, array $dictionary = array());
}