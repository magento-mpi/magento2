<?php
/**
 * Default configuration data reader. Reads configuration data from storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Initial;

class Reader
{
    /**
     * File locator
     *
     * @var \Magento\Config\FileResolverInterface
     */
    protected $_fileResolver;

    /**
     * Config converter
     *
     * @var \Magento\Core\Model\Config\Initial\Converter
     */
    protected $_converter;

    /**
     * Config file name
     *
     * @var string
     */
    protected $_fileName;

    /**
     * Class of dom configuration document used for merge
     *
     * @var string
     */
    protected $_domDocumentClass;

    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('primary', 'global');

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Core\Model\Config\Initial\Converter $converter
     * @param string $fileName
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Core\Model\Config\Initial\Converter $converter,
        $fileName = 'config.xml',
        $domDocumentClass = 'Magento\Config\Dom'
    ) {
        $this->_fileResolver = $fileResolver;
        $this->_converter = $converter;
        $this->_domDocumentClass = $domDocumentClass;
        $this->_fileName = $fileName;
    }

    /**
     * Load configuration scope
     *
     * @return array
     *
     * @throws \Magento\Exception
     */
    public function read()
    {
        $fileList = array();
        foreach ($this->_scopePriorityScheme as $scope) {
            $fileList = array_merge($fileList, $this->_fileResolver->get($this->_fileName, $scope));
        }

        if (!count($fileList)) {
            return array();
        }

        /** @var \Magento\Config\Dom $domDocument */
        $domDocument = null;
        foreach ($fileList as $file) {
            try {
                if (is_null($domDocument)) {
                    $class = $this->_domDocumentClass;
                    $domDocument = new $class(file_get_contents($file));
                } else {
                    $domDocument->merge(file_get_contents($file));
                }
            } catch (\Magento\Config\Dom\ValidationException $e) {
                throw new \Magento\Exception("Invalid XML in file " . $file . ":\n" . $e->getMessage());
            }
        }

        $output = array();
        if ($domDocument) {
            $output = $this->_converter->convert($domDocument->getDom());
        }
        return $output;
    }

}
