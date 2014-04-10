<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

class ReinitableConfig extends \Magento\Framework\App\MutableScopeConfig implements \Magento\Framework\App\Config\ReinitableConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function reinit()
    {
        $this->_scopePool->clean();
        return $this;
    }
}
