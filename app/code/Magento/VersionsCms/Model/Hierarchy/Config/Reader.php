<?php
/**
 * Cms menu hierarchy config reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Hierarchy\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/menuLayout' => 'name',
        '/config/menuLayout/pageLayout' => 'handle',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\VersionsCms\Model\Hierarchy\Config\Converter $converter
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param \Magento\VersionsCms\Model\Hierarchy\Config\SchemaLocator $schemaLocator
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\VersionsCms\Model\Hierarchy\Config\Converter $converter,
        \Magento\VersionsCms\Model\Hierarchy\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'menu_hierarchy.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }

}
