<?php

namespace Magento\Composer\Log;

interface Writer {
    public function write($message);
}