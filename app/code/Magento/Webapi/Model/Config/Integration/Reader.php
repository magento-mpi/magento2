<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Config\Integration;

/**
 * Service config data reader.
 */
class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Integration\Model\Config\Api\Converter $converter
     * @param \Magento\Integration\Model\Config\Api\SchemaLocator $schemeLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Integration\Model\Config\Api\Converter $converter,
        \Magento\Integration\Model\Config\Api\SchemaLocator $schemeLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'integration\api.xml'
    ) {
        parent::__construct($fileResolver, $converter, $schemeLocator, $validationState, $fileName);
    }
}
