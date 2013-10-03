<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GiftRegistry configuration filesystem loader. Loads gift registry configuration from XML file
 */
namespace Magento\GiftRegistry\Model\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{

    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/attribute_type' => 'name',
        '/config/attribute_group' => 'name',
        '/config/registry/static_attribute' => 'name',
        '/config/registry/custom_attribute' => 'name',
        '/config/registrant/static_attribute' => 'name',
        '/config/registrant/custom_attribute' => 'name'
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\GiftRegistry\Model\Config\Converter $converter
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param \Magento\GiftRegistry\Model\Config\SchemaLocator $schemaLocator
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\GiftRegistry\Model\Config\Converter $converter,
        \Magento\GiftRegistry\Model\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'giftregistry.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
