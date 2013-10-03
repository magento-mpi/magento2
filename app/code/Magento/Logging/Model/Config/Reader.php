<?php
/**
 * Reader class for logging.xml
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/logging/actions/action' => 'id',
        '/logging/groups/group' => 'name',
        '/logging/groups/group/events/event' => 'controller_action',
        '/logging/groups/group/events/event/expected_models/expected_model' => 'class',
        '/logging/groups/group/events/event/expected_models/expected_model/additional_fields/field' => 'name',
        '/logging/groups/group/events/event/expected_models/expected_model/skip_fields/field' => 'name',
        '/logging/groups/group/events/event/skip_on_back/controller_action' => 'name',
        '/logging/groups/group/expected_models/expected_model' => 'class',
        '/logging/groups/group/expected_models/expected_model/additional_fields/field' => 'name',
        '/logging/groups/group/expected_models/expected_model/skip_fields/field' => 'name',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Logging\Model\Config\Converter $converter
     * @param \Magento\Logging\Model\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Logging\Model\Config\Converter $converter,
        \Magento\Logging\Model\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'logging.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState,
            $fileName, $idAttributes, $domDocumentClass
        );
    }
}
