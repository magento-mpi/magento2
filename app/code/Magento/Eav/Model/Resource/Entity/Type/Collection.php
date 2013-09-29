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
 * Eav Resource Entity Type Collection Model
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Resource\Entity\Type;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Eav\Model\Entity\Type', 'Magento\Eav\Model\Resource\Entity\Type');
    }
}
