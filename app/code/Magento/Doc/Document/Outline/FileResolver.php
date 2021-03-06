<?php
/**
 * Application config file resolver
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Doc\Document\Outline;

use Magento\Framework\View\DesignInterface;

/**
 * Class FileResolver
 * @package Magento\Doc\Document\Outline
 */
class FileResolver
{
    /**
     * @var \Magento\Core\Model\Theme
     */
    protected $theme;

    /**
     * @var Collector
     */
    protected $collector;

    /**
     * @param DesignInterface $design
     * @param Collector $collector
     */
    public function __construct(
        DesignInterface $design,
        Collector $collector
    ) {
        $this->theme = $design->getDesignTheme();
        $this->collector = $collector;
    }

    /**
     * {@inheritdoc}
     */
    public function get($filename)
    {
        $result = $this->collector->getFiles($this->theme, $filename);
        return $result;
    }
}
