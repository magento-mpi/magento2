<?php
/**
 * URL Rewrite Type Provider
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model\UrlRewrite;

use Magento\Framework\Option\ArrayInterface;

class TypeProvider implements ArrayInterface
{
    const SYSTEM = 1;

    const CUSTOM = 0;

    /**
     * @var array|null
     */
    protected $_options = null;

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                self::SYSTEM => __('System'),
                self::CUSTOM => __('Custom'),
            );
        }
        return $this->_options;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
