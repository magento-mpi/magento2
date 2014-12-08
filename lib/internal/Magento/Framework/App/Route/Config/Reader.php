<?php
/**
 * Routes configuration reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Route\Config;

class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * List of paths to identifiable nodes
     *
     * @var array
     */
    protected $_idAttributes = [
        '/config/router' => 'id',
        '/config/router/route' => 'id',
        '/config/router/route/module' => 'name',
    ];

    /**
     * @param \Magento\Framework\Config\FileResolverInterface $fileResolver
     * @param Converter $converter
     * @param SchemaLocator $schemaLocator
     * @param \Magento\Framework\Config\ValidationStateInterface $validationState
     * @param string $fileName
     */
    public function __construct(
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $fileName = 'routes.xml'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $this->_idAttributes
        );
    }
}
