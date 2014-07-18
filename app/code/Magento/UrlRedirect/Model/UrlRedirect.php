<?php
/**
 * URL Rewrite Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Model;

class UrlRedirect extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize corresponding resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\UrlRedirect\Model\Resource\UrlRedirect');
    }
}
