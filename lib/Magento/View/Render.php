<?php

namespace Magento\View;

interface Render
{
    public function renderTemplate($template, array $data);

    public function renderContainer($content, array $containerInfo = array());
}
