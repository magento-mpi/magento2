<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1\Data;

use Magento\Framework\Service\SimpleDataObjectConverter;
use Magento\UrlRedirect\Service\V1\Data\UrlRewriteBuilderFactory;

/**
 * Data object converter
 */
class Converter
{
    /**
     * @var UrlRewriteBuilderFactory
     */
    protected $builderFactory;

    /**
     * @param UrlRewriteBuilderFactory $builderFactory
     */
    public function __construct(UrlRewriteBuilderFactory $builderFactory)
    {
        $this->builderFactory = $builderFactory;
    }

    /**
     * Convert array to Service Data Object
     *
     * @param array $data
     * @return UrlRewrite
     */
    public function convertArrayToObject(array $data)
    {
        return $this->builderFactory->create()->populateWithArray($data)->create();
    }

    /**
     * Convert Service Data Object to array
     *
     * @param UrlRewrite $object
     * @return array
     */
    public function convertObjectToArray(UrlRewrite $object)
    {
        return SimpleDataObjectConverter::toFlatArray($object);
    }
}
