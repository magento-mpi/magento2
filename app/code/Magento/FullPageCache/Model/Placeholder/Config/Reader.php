<?php
/**
 * Placeholders configuration filesystem loader. Loads placeholders configuration from XML files, split by scopes
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Placeholder\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/placeholder' => 'name',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\FullPageCache\Model\Placeholder\Config\Converter $converter
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param \Magento\FullPageCache\Model\Placeholder\Config\SchemaLocator $schemaLocator
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\FullPageCache\Model\Placeholder\Config\Converter $converter,
        \Magento\FullPageCache\Model\Placeholder\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'placeholders.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }

}
