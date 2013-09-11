<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Url rewrite suffix backend
 */
namespace Magento\Catalog\Model\System\Config\Backend\Catalog\Url\Rewrite;

class Suffix extends \Magento\Core\Model\Config\Value
{
    /**
     * Check url rewrite suffix - whether we can support it
     *
     * @return \Magento\Catalog\Model\System\Config\Backend\Catalog\Url\Rewrite\Suffix
     */
    protected function _beforeSave()
    {
        \Mage::helper('Magento\Core\Helper\Url\Rewrite')->validateSuffix($this->getValue());
        return $this;
    }
}
