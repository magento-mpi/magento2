<?php

namespace Magento\View\Render;

use Magento\View\Render;
use Magento\View\TemplateEngineFactory;

class Html implements Render
{
    const TYPE_HTML = 'html';

    /**
     * @var TemplateEngineFactory
     */
    protected $templateEngineFactory;

    /**
     * @param TemplateEngineFactory $templateEngineFactory
     */
    public function __construct(TemplateEngineFactory $templateEngineFactory)
    {
        $this->templateEngineFactory = $templateEngineFactory;
    }

    public function renderTemplate($template, array $data)
    {
        $result = $this->fetchView($template, $data);

        // wrap block's result with ui data containers

        return $result;
    }

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

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Retrieve rendered output
     *
     * @param $template
     * @param array $data
     * @return string
     */
    protected function fetchView($template, array $data = array())
    {
        $extension = pathinfo($template, PATHINFO_EXTENSION);
        $templateEngine = $this->templateEngineFactory->get($extension);
        $result = $templateEngine->render($template, $data);

        return $result;
    }
}