<?php
/**
 * Google Optmizer Product Block
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Block\Code;

class Product extends \Magento\GoogleOptimizer\Block\AbstractCode implements \Magento\View\Block\IdentityInterface
{
    /**
     * @var Product name in registry
     */
    protected $_registryName = 'current_product';

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->_getEntity()->getIdentities();
    }
}
