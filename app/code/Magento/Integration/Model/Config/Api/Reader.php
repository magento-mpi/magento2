<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Config\Api;

/**
 * Service config data reader.
 */
class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/service' => 'class',
        '/config/service/rest-route' => 'method',
    );

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
