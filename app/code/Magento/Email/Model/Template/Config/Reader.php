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
     * @param \Magento\Email\Model\Template\Config\FileResolver $fileResolver
     * @param \Magento\Email\Model\Template\Config\Converter $converter
     * @param \Magento\Email\Model\Template\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     */
    public function __construct(
        \Magento\Email\Model\Template\Config\FileResolver $fileResolver,
        \Magento\Email\Model\Template\Config\Converter $converter,
        \Magento\Email\Model\Template\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState
    ) {
        $fileName = 'email_templates.xml';
        $idAttributes = array(
            '/config/template' => 'id',
        );
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes);
    }
}
