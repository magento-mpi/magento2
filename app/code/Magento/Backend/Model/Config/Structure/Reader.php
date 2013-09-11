<?php
/**
 * Backend System Configuration reader.
 * Retrieves system configuration form layout from system.xml files. Merges configuration and caches it.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Structure;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/system/tab' => 'id',
        '/config/system/section' => 'id',
        '/config/system/section/group' => 'id',
        '/config/system/section/group/field' => 'id',
        '/config/system/section/group/field/depends/field' => 'id',
        '/config/system/section/group/group' => 'id',
        '/config/system/section/group/group/field' => 'id',
        '/config/system/section/group/group/field/depends/field' => 'id',
        '/config/system/section/group/group/group' => 'id',
        '/config/system/section/group/group/group/field' => 'id',
        '/config/system/section/group/group/group/field/depends/field' => 'id',
        '/config/system/section/group/group/group/group' => 'id',
        '/config/system/section/group/group/group/group/field' => 'id',
        '/config/system/section/group/group/group/group/field/depends/field' => 'id',
        '/config/system/section/group/group/group/group/group' => 'id',
        '/config/system/section/group/group/group/group/group/field' => 'id',
        '/config/system/section/group/group/group/group/group/field/depends/field' => 'id',
        '/config/system/section/group/field/options/option' => 'label',
        '/config/system/section/group/group/field/options/option' => 'label',
        '/config/system/section/group/group/group/field/options/option' => 'label',
        '/config/system/section/group/group/group/group/field/options/option' => 'label',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Backend\Model\Config\Structure\Converter $converter
     * @param \Magento\Backend\Model\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Backend\Model\Config\Structure\Converter $converter,
        \Magento\Backend\Model\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'system.xml',
        $idAttributes = array(),
        $domDocumentClass = '\Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
