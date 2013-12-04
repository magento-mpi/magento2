<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model\Config;

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
        '/integrations/integration' => 'name'
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Integration\Model\Config\Converter $converter
     * @param \Magento\Integration\Model\Config\SchemaLocator $schemeLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Integration\Model\Config\Converter $converter,
        \Magento\Integration\Model\Config\SchemaLocator $schemeLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'integration\config.xml'
    ) {
        parent::__construct($fileResolver, $converter, $schemeLocator, $validationState, $fileName);
    }
}
