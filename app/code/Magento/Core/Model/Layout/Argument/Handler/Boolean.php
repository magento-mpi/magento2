<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Layout\Argument\Handler;

/**
 * Layout argument. Type boolean.
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Boolean extends \Magento\Core\Model\Layout\Argument\AbstractHandler
{
    /**
     * Process argument value
     *
     * @param array $argument
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function process(array $argument)
    {
        $this->_validate($argument);
        return filter_var($argument['value'], FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param array $argument
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function _validate(array $argument)
    {
        parent::_validate($argument);
        if (!in_array($argument['value'], array('true', 'false'))) {
            throw new \InvalidArgumentException('Value is not boolean argument. ' . $this->_getArgumentInfo($argument));
        }
    }
}
