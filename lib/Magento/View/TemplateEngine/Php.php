<?php
/**
 * Template engine that enables PHP templates to be used for rendering
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\TemplateEngine;

use Magento\View\TemplateEngine;

class Php implements TemplateEngine
{
    /**
     * Include the named PHTML template.
     *
     * @param string $fileName
     * @param array $data
     *
     * @return string
     * @throws \Exception any exception that the template may throw
     */
    public function render($fileName, array $data = array())
    {
        ob_start();
        try {
            extract($data, EXTR_SKIP);
            include $fileName;
        } catch (\Exception $exception) {
            ob_end_clean();
            throw $exception;
        }
        return ob_get_clean();
    }
}
