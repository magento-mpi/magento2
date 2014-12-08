<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Block\Controller;

use Magento\Framework\View\Block\IdentityInterface;
use Magento\Framework\View\Element\AbstractBlock;

class StubBlock extends AbstractBlock implements IdentityInterface
{
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return ['identity1', 'identity2'];
    }
}
