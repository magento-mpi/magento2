<?php
/**
 * Resources configuration filesystem loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/resource' => 'name'
    );

    /**
     * @var \Magento\Core\Model\Config\Local
     */
    protected $_configLocal;

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Core\Model\Resource\Config\Converter $converter
     * @param \Magento\Core\Model\Resource\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param \Magento\Core\Model\Config\Local $configLocal
     * @param string $fileName
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Core\Model\Resource\Config\Converter $converter,
        \Magento\Core\Model\Resource\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        \Magento\Core\Model\Config\Local $configLocal,
        $fileName = 'resources.xml'
    ) {
        $this->_configLocal = $configLocal;
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName);
    }

    /**
     * Load configuration scope
     *
     * @param string|null $scope
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function read($scope = null)
    {
        $data = parent::read();
        $data = array_replace($data, $this->_configLocal->getResources());

        return $data;
    }
}
