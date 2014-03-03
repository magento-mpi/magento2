<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model;

/**
 * EAV entity model
 *
 * @category   Magento
 * @package    Magento_Eav
 */
class Entity extends \Magento\Eav\Model\Entity\AbstractEntity
{
    const DEFAULT_ENTITY_MODEL      = 'Magento\Eav\Model\Entity';
    const DEFAULT_ATTRIBUTE_MODEL   = 'Magento\Eav\Model\Entity\Attribute';
    const DEFAULT_BACKEND_MODEL     = 'Magento\Eav\Model\Entity\Attribute\Backend\DefaultBackend';
    const DEFAULT_FRONTEND_MODEL    = 'Magento\Eav\Model\Entity\Attribute\Frontend\DefaultFrontend';
    const DEFAULT_SOURCE_MODEL      = 'Magento\Eav\Model\Entity\Attribute\Source\Config';

    const DEFAULT_ENTITY_TABLE      = 'eav_entity';
    const DEFAULT_ENTITY_ID_FIELD   = 'entity_id';

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Entity\Attribute\Set $attrSetEntity
     * @param \Magento\LocaleInterface $locale
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Validator\UniversalFactory $universalFactory
     * @param array $data
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\Attribute\Set $attrSetEntity,
        \Magento\LocaleInterface $locale,
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Validator\UniversalFactory $universalFactory,
        $data = array()
    ) {
        parent::__construct(
            $resource,
            $eavConfig,
            $attrSetEntity,
            $locale,
            $resourceHelper,
            $universalFactory,
            $data
        );
        $this->setConnection($resource->getConnection('eav_read'));
    }
}
