<?php

interface Mage_Api2_Model_Renderer_Interface
{
    public function render(array $data);

    /**
     * Render error in a certain format
     *
     * @abstract
     * @param int $code
     * @param array $exceptions
     * @return void
     */
    public function renderErrors($code, $exceptions);
}
