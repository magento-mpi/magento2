<?php
/**
 * URL Rewrite Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model;

/**
 * @method string getEntityType()
 * @method UrlRewrite setEntityType(string $value)
 * @method int getEntityId()
 * @method UrlRewrite setEntityId(int $value)
 */
class UrlRewrite extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize corresponding resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\UrlRewrite\Model\Resource\UrlRewrite');
    }
}
