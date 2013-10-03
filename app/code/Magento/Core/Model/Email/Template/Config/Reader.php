<?php
/**
 * Loads email template configuration from multiple XML files by merging them together
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Email\Template\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * @var \Magento\Core\Model\Module\Dir\ReverseResolver
     */
    private $_moduleDirResolver;

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Core\Model\Email\Template\Config\Converter $converter
     * @param \Magento\Core\Model\Email\Template\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param \Magento\Core\Model\Module\Dir\ReverseResolver $moduleDirResolver
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Core\Model\Email\Template\Config\Converter $converter,
        \Magento\Core\Model\Email\Template\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        \Magento\Core\Model\Module\Dir\ReverseResolver $moduleDirResolver
    ) {
        $fileName = 'email_templates.xml';
        $idAttributes = array(
            '/config/template' => 'id',
        );
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes);
        $this->_moduleDirResolver = $moduleDirResolver;
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
