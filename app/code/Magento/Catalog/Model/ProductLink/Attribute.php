<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\ProductLink;

use \Magento\Catalog\Api\Data\ProductLinkAttributeInterface;

/**
 * @codeCoverageIgnore
 */
class Attribute extends \Magento\Framework\Api\AbstractExtensibleObject implements ProductLinkAttributeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->_get('code');
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_get('type');
    }
}
