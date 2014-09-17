<?php
/**
 * Application config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Config;

use Magento\Framework\Config\FileResolverInterface;
use Magento\Ui\Config\Collector\CollectorInterface;


/**
 * Class FileResolver
 * @package Magento\Ui\Config
 */
class FileResolver implements FileResolverInterface
{
    /**
     * @var CollectorInterface
     */
    protected $collector;

    /**
     * @param CollectorInterface $collector
     */
    public function __construct(CollectorInterface $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @param string $filename
     * @param string $scope
     * @return \Magento\Framework\View\File[]
     */
    public function get($filename, $scope)
    {
        $result = $this->collector->getFiles($filename);
        return $result;
    }
}
