<?php
/**
 * Collection of the available product link types
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

class LinkTypeProvider
{
    /**
     * Available product link types
     *
     * Represented by an assoc array with the following format 'product_link_name' => 'product_link_code'
     *
     * @var array
     */
    protected $_linkTypes;

    /**
     * @param array $linkTypes
     */
    public function __construct(
        array $linkTypes = array()
    ) {
        $this->_linkTypes = $linkTypes;
    }

    /**
     * Retrieve information about available product link types
     *
     * @return array
     */
    public function getLinkTypes()
    {
        return $this->_linkTypes;
    }
}
