<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

use \Magento\Catalog\Model\Product\LinkTypeProvider;

class LinkTypeResolver
{
    /**
     * @param LinkTypeProvider $linkTypeProvider
     */
    public function __construct(LinkTypeProvider $linkTypeProvider)
    {
        $this->linkTypeProvider = $linkTypeProvider;
    }

    /**
     * @var LinkTypeProvider
     */
    protected $linkTypeProvider;

    /**
     * Get link type id by code
     *
     * @param string $code
     * @return int
     * @throws \InvalidArgumentException
     */
    public function getTypeIdByCode($code)
    {
        $types = $this->linkTypeProvider->getLinkTypes();
        if (isset($types[$code])) {
            return $types[$code];
        }
        throw new \InvalidArgumentException('Unknown link type code is provided');
    }
}
