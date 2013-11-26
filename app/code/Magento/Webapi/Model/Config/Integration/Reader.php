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
     * @param \Magento\Webapi\Model\Config\Integration\Converter $converter
     * @param \Magento\Webapi\Model\Config\Integration\SchemaLocator $schemeLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Webapi\Model\Config\Integration\Converter $converter,
        \Magento\Webapi\Model\Config\Integration\SchemaLocator $schemeLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'integration\api.xml'
    ) {
        parent::__construct($fileResolver, $converter, $schemeLocator, $validationState, $fileName);
    }
}
