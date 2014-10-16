<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

use \Magento\Catalog\Model\Product\LinkTypeProvider;
use \Magento\Framework\Exception\NoSuchEntityException;

class LinkTypeResolver
{
    /**
     * @var LinkTypeProvider
     */
    protected $linkTypeProvider;

    /**
     * @param LinkTypeProvider $linkTypeProvider
     */
    public function __construct(LinkTypeProvider $linkTypeProvider)
    {
        $this->linkTypeProvider = $linkTypeProvider;
    }

    /**
     * Get link type id by code
     *
     * @param string $code
     * @throws NoSuchEntityException
     * @return int
     */
    public function getTypeIdByCode($code)
    {
        $types = $this->linkTypeProvider->getLinkTypes();
        if (isset($types[$code])) {
            return $types[$code];
        }
        throw new NoSuchEntityException('Unknown link type code is provided');
    }
}
