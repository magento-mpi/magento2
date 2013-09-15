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
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

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
        $value = $argument['value'];

        return $this->_objectManager->create($value['object']);
    }

    /**
     * Validate argument
     * @param $argument
     * @throws \InvalidArgumentException
     */
    protected function _validate(array $argument)
    {
        parent::_validate($argument);
        $value = $argument['value'];

        if (!isset($value['object'])) {
            throw new \InvalidArgumentException(
                'Passed value has incorrect format. ' . $this->_getArgumentInfo($argument)
            );
        }

        if (!is_subclass_of($value['object'], 'Magento\Data\Collection')) {
            throw new \InvalidArgumentException(
                'Incorrect data source model. ' . $this->_getArgumentInfo($argument)
            );
        }
    }

    /**
     * Retrieve value from argument
     *
     * @param \Magento\Core\Model\Layout\Element $argument
     * @return array|null
     */
    protected function _getArgumentValue(\Magento\Core\Model\Layout\Element $argument)
    {
        $value = parent::_getArgumentValue($argument);
        if (!isset($value)) {
            return null;
        }
        return array(
            'object' => $value
        );
    }
}
