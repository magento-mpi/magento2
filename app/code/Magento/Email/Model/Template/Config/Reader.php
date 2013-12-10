<?php
/**
 * Loads email template configuration from multiple XML files by merging them together
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Model\Template\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * @var \Magento\Module\Dir\ReverseResolver
     */
    private $_moduleDirResolver;

    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes =  array(
        '/config/template' => 'id',
    );

    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Email\Model\Template\Config\Converter $converter,
        \Magento\Email\Model\Template\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        \Magento\Module\Dir\ReverseResolver $moduleDirResolver,
        $fileName = 'email_templates.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom',
        $defaultScope = 'global'
    ) {
        $this->_moduleDirResolver = $moduleDirResolver;
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
     * Add information on context of a module, config file belongs to
     *
     * {@inheritdoc}
     * @throws \UnexpectedValueException
     */
    protected function _readFileContents($filename)
    {
        $result = parent::_readFileContents($filename);
        $moduleName = $this->_moduleDirResolver->getModuleName($filename);
        if (!$moduleName) {
            throw new \UnexpectedValueException("Unable to determine a module, file '$filename' belongs to.");
        }
        $result = str_replace('<template ', '<template module="' . $moduleName . '" ', $result);
        return $result;
    }
}
