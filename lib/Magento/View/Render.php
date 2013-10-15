<?php

namespace Magento\View;

interface Render
{
    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public function renderTemplate($template, array $data);

    /**
     * @param string $content
     * @param array $containerInfo
     * @return string
     */
    public function renderContainer($content, array $containerInfo = array());
}
