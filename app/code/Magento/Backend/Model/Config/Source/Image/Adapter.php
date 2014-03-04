<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Source\Image;

class Adapter implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\Image\Adapter\ConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\Image\Adapter\ConfigInterface $config
     */
    public function __construct(\Magento\Image\Adapter\ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Return hash of image adapter codes and labels
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = array();
        foreach ($this->config->getAdapters() as $alias => $adapter) {
            $result[$alias] = __($adapter['title']);
        }

        return $result;
    }
}
