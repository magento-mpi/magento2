<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Render;

use Magento\View\Render;
use Magento\View\TemplateEngineFactory;

class Json implements Render
{
    /**
     * Render type
     */
    const TYPE_JSON = 'json';

    /**
     * @param string $template
     * @param array $data
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function renderTemplate($template, array $data)
    {
        return json_encode($data);
    }

    /**
     * @param string $content
     * @param array $containerInfo
     * @return string
     * @todo
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function renderContainer($content, array $containerInfo = array())
    {
        return '';
    }
}