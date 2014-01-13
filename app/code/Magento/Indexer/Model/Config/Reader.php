<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Config\ConverterInterface $converter,
        \Magento\Config\SchemaLocatorInterface $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'indexer.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom',
        $defaultScope = 'global'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }
}
