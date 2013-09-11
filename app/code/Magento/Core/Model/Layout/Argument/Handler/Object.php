<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout argument. Type object
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Layout\Argument\Handler;

class Object extends \Magento\Core\Model\Layout\Argument\HandlerAbstract
{
    /**
     * Instantiate model object
     * @param string $value
     * @return \Magento\Core\Model\AbstractModel|boolean
     */
    public function process($value)
    {
        $valueInstance = $this->_objectManager->create($value, array());
        return $valueInstance;
    }
}
