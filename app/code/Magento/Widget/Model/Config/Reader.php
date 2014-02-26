<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Model\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/widgets/widget' => 'id',
        '/widgets/widget/parameters/parameter' => 'name',
        '/widgets/widget/parameters/parameter/options/option' => 'name',
        '/widgets/widget/containers/container' => 'name',
        '/widgets/widget/containers/container/template' => 'name',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param Converter $converter
     * @param \Magento\Config\SchemaLocatorInterface $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        Converter $converter,
        \Magento\Config\SchemaLocatorInterface $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'widget.xml',
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

    /**
     * Load configuration file
     *
     * @param string $file
     * @return array
     */
    public function readFile($file)
    {
        return $this->_readFiles(array($file));
    }
}
