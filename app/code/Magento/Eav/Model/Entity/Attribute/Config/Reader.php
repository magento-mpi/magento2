<?php
/**
 * Attribute configuration reader
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * Xml merging attributes
     *
     * @var array
     */
    protected $_idAttributes = array(
        'config/entity' => 'type',
        'config/entity/attribute' => 'code',
        'config/entity/attribute/field' => 'code'
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Eav\Model\Entity\Attribute\Config\Converter $converter
     * @param \Magento\Eav\Model\Entity\Attribute\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Eav\Model\Entity\Attribute\Config\Converter $converter,
        \Magento\Eav\Model\Entity\Attribute\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState
    ) {
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, 'eav_attributes.xml', array());
    }
}
