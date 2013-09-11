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
    const DEFAULT_ENTITY_MODEL      = '\Magento\Eav\Model\Entity';
    const DEFAULT_ATTRIBUTE_MODEL   = '\Magento\Eav\Model\Entity\Attribute';
    const DEFAULT_BACKEND_MODEL     = '\Magento\Eav\Model\Entity\Attribute\Backend\DefaultBackend';
    const DEFAULT_FRONTEND_MODEL    = '\Magento\Eav\Model\Entity\Attribute\Frontend\DefaultFrontend';
    const DEFAULT_SOURCE_MODEL      = '\Magento\Eav\Model\Entity\Attribute\Source\Config';

    const DEFAULT_ENTITY_TABLE      = 'eav_entity';
    const DEFAULT_ENTITY_ID_FIELD   = 'entity_id';

    /**
     * Resource initialization
     */
    public function __construct()
    {
        $resource = \Mage::getSingleton('Magento\Core\Model\Resource');
        $this->setConnection($resource->getConnection('eav_read'));
    }

}
