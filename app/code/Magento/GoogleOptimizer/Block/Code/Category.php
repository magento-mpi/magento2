<?php
/**
 * Google Optimizer Category Block
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Block\Code;

class Category extends \Magento\GoogleOptimizer\Block\AbstractCode implements \Magento\View\Block\IdentityInterface
{
    /**
     * @var string Entity name in registry
     */
    protected $_registryName = 'current_category';

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
