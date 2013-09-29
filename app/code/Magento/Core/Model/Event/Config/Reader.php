<?php
/**
 * Event observers configuration filesystem loader. Loads event observers configuration from XML files, split by scopes
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Event\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/event' => 'name',
        '/config/event/observer' => 'name',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Core\Model\Event\Config\Converter $converter
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param \Magento\Core\Model\Event\Config\SchemaLocator $schemaLocator
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Core\Model\Event\Config\Converter $converter,
        \Magento\Core\Model\Event\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'events.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }

}
