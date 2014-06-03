<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cron\Model\Groups\Config\Reader;

/**
 * Reader for XML files
 */
class Xml extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * Mapping XML name nodes
     *
     * @var array
     */
    protected $_idAttributes = array('/config/group' => 'id');

    /**
     * @param \Magento\Framework\Config\FileResolverInterface $fileResolver
     * @param \Magento\Cron\Model\Groups\Config\Converter\Xml $converter
     * @param \Magento\Cron\Model\Groups\Config\SchemaLocator $schemaLocator
     * @param \Magento\Framework\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Cron\Model\Groups\Config\Converter\Xml $converter,
        \Magento\Cron\Model\Groups\Config\SchemaLocator $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $fileName = 'cron_groups.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Framework\Config\Dom',
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
