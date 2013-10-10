<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV entity model
 *
 * @category   Magento
 * @package    Magento_Eav
 */
namespace Magento\Eav\Model;

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
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Entity\Attribute\Set $attrSetEntity
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Validator\UniversalFactory $universalFactory
     * @param \Magento\Core\Model\Resource $coreResource
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Resource $resource,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\Attribute\Set $attrSetEntity,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Validator\UniversalFactory $universalFactory,
        \Magento\Core\Model\Resource $coreResource,
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
        $this->setConnection($coreResource->getConnection('eav_read'));
    }
}
