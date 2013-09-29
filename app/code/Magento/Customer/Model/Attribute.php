<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer attribute model
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model;

class Attribute extends \Magento\Eav\Model\Attribute
{
    /**
     * Name of the module
     */
    const MODULE_NAME = 'Magento_Customer';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'customer_entity_attribute';

    /**
     * Prefix of model events object
     *
     * @var string
     */
    protected $_eventObject = 'attribute';

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Customer\Model\Resource\Attribute');
    }
}
