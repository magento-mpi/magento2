<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Block\Controller;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Block\IdentityInterface;

class StubBlock extends AbstractBlock implements IdentityInterface
{
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return array('identity1', 'identity2');
    }
} 