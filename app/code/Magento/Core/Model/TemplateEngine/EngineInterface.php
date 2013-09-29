<?php
/**
 * Interface for Template Engine
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine;

interface EngineInterface
{
    /**
     * Render the named template in the context of a particular block and with
     * the data provided in $vars.
     *
     * @param \Magento\Core\Block\Template $block
     * @param $templateFile
     * @param array $dictionary
     * @return string rendered template
     */
    public function render(\Magento\Core\Block\Template $block, $templateFile, array $dictionary = array());
}
