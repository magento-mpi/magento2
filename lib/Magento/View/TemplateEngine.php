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

interface TemplateEngine
{
    /**
     * Render the named template with the data provided.
     *
     * @param $templateFile
     * @param array $data
     * @return string rendered template
     */
    public function render($templateFile, array $data = array());
}