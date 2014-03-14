<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class ReinitableConfig extends Config implements ReinitableConfigInterface
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
