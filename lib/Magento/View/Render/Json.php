<?php

namespace Magento\View\Render;

use Magento\View\Render;
use Magento\View\TemplateEngineFactory;

class Json implements Render
{
    const TYPE_JSON = 'json';

    public function renderTemplate($template, array $data)
    {
        $result = json_encode($data);
        return $result;
    }

    public function renderContainer($content, array $containerInfo = array())
    {
        //
    }
}