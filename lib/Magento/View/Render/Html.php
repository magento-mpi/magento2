<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Render;

use Magento\View\RenderInterface;
use Magento\View\TemplateEngineFactory;

class Html implements RenderInterface
{
    /**
     * Render type
     */
    const TYPE_HTML = 'html';

    /**
     * @var TemplateEngineFactory
     */
    protected $templateFactory;

    /**
     * @param TemplateEngineFactory $templateFactory
     */
    public function __construct(
        TemplateEngineFactory $templateFactory
    ) {
        $this->templateFactory = $templateFactory;
    }

    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public function renderTemplate($template, array $data)
    {
        // wrap block's result with ui data containers
        return $this->fetchView($template, $data);
    }

    /**
     * @param string $content
     * @param array $containerInfo
     * @return string
     */
    public function renderContainer($content, array $containerInfo = array())
    {
        if (isset($containerInfo['tag'])) {
            $htmlId = $htmlClass = '';
            if (isset($containerInfo['id'])) {
                $htmlId = ' id="' . $containerInfo['id']. '"';
            }
            if (isset($containerInfo['class'])) {
                $htmlClass = ' class="'. $containerInfo['class'] . '"';
            }
            $content = sprintf('<%1$s%2$s%3$s>%4$s</%1$s>', $containerInfo['tag'], $htmlId, $htmlClass, $content);
        }
        return $content;
    }

    /**
     * Retrieve rendered output
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function fetchView($template, array $data = array())
    {
        $extension = pathinfo($template, PATHINFO_EXTENSION);
        $templateEngine = $this->templateFactory->get($extension);
        return $templateEngine->render($template, $data);
    }
}