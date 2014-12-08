<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View;

/**
 * Interface RenderInterface
 */
interface RenderInterface
{
    /**
     * Render template
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public function renderTemplate($template, array $data);

    /**
     * Render container
     *
     * @param string $content
     * @param array $containerInfo
     * @return string
     */
    public function renderContainer($content, array $containerInfo = []);
}
