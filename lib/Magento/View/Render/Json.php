<?php

namespace Magento\View\Render;

use Magento\View\Render;
use Magento\View\TemplateEngineFactory;

class Json implements Render
{
    const TYPE_JSON = 'json';

    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public function renderTemplate($template, array $data)
    {
        $result = json_encode($data);
        return $result;
    }

    /**
     * @param string $content
     * @param array $containerInfo
     */
    public function renderContainer($content, array $containerInfo = array())
    {
        //
    }
}