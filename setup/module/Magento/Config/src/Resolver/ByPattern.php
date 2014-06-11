<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Config\Resolver;

use Magento\Config\GlobWrapper;
use Magento\Config\Resolver;

class ByPattern implements Resolver
{
    protected $pattern;

    protected $path;

    protected $glob;

    public function __construct(
        GlobWrapper $glob,
        $path,
        $pattern
    ) {
        $this->glob = $glob;
        $this->path = $path;
        $this->pattern = $pattern;
    }

    public function get()
    {
        return $this->glob->glob($this->path . $this->pattern);
    }
}
