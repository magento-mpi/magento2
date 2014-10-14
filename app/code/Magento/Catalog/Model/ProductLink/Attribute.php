<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\ProductLink;

class Attribute implements \Magento\Catalog\Api\Data\ProductLinkAttributeInterface
{
    /**
     * @var string[]
     */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get object key
     *
     * @return string
     */
    public function getKey()
    {
        return isset($this->data[self::KEY]) ? $this->data[self::KEY] : null;
    }

    /**
     * Get object value
     *
     * @return string
     */
    public function getValue()
    {
        return isset($this->data[self::VALUE]) ? $this->data[self::VALUE] : null;
    }
}
