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
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var \Magento\Config\GlobWrapper
     */
    protected $glob;

    /**
     * @param GlobWrapper $glob
     * @param string $path
     * @param string $pattern
     */
    public function __construct(
        GlobWrapper $glob,
        $path,
        $pattern
    ) {
        $this->glob = $glob;
        $this->path = $path;
        $this->pattern = $pattern;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->glob->glob($this->path . $this->pattern);
    }
}
