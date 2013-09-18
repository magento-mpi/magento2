<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reader for XML files
 */
class Magento_Cron_Model_Config_Reader_Xml extends Magento_Config_Reader_Filesystem
{
    /**
     * Mapping XML name nodes
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/job' => 'name'
    );

    /**
     * Initialize parameters
     *
     * @param Magento_Config_FileResolverInterface    $fileResolver
     * @param Magento_Config_ConverterInterface       $converter
     * @param Magento_Config_SchemaLocatorInterface   $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param string                                  $fileName
     * @param array                                   $idAttributes
     * @param string                                  $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Config_ConverterInterface $converter,
        Magento_Config_SchemaLocatorInterface $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'crontab.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
