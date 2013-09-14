<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Locale\Hierarchy\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/locale' => 'code',
    );

    /**
     * @param \Magento\Core\Model\Locale\Hierarchy\Config\FileResolver $fileResolver
     * @param \Magento\Core\Model\Locale\Hierarchy\Config\Converter $converter
     * @param \Magento\Core\Model\Locale\Hierarchy\Config\SchemaLocator $schemeLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Core\Model\Locale\Hierarchy\Config\FileResolver $fileResolver,
        \Magento\Core\Model\Locale\Hierarchy\Config\Converter $converter,
        \Magento\Core\Model\Locale\Hierarchy\Config\SchemaLocator $schemeLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'config.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemeLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
