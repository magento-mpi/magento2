<?php
/**
 * Default route path for doc area
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\App;

/**
 * Class DefaultPath
 * @package Magento\Doc\App
 */
class DefaultPath implements \Magento\Framework\App\DefaultPathInterface
{
    /**
     * Define default values for path chunks
     *
     * @var array
     */
    protected $parts = [
        'area' => 'doc'
    ];

    /**
     * Retrieve default path part by code
     *
     * @param string $code
     * @return string
     */
    public function getPart($code)
    {
        return isset($this->parts[$code]) ? $this->parts[$code] : null;
    }
}
