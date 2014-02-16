<?php
/**
 * Routes configuration reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Route\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of paths to identifiable nodes
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/router'               => 'id',
        '/config/router/route'         => 'id',
        '/config/router/route/module'  => 'name'
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\App\Route\Config\Converter $converter
     * @param \Magento\App\Route\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\App\Route\Config\Converter $converter,
        \Magento\App\Route\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'routes.xml'
    ) {
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName,
            $this->_idAttributes);
    }
}
