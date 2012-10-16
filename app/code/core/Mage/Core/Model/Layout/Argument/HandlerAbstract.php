<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout object abstract argument
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Layout_Argument_HandlerAbstract
    implements Mage_Core_Model_Layout_Argument_HandlerInterface
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * Common constructor of argument types
     * @param array $args
     * @throws InvalidArgumentException
     */
    public function __construct(array $args = array())
    {
        if (!isset($args['objectFactory'])) {
            throw new InvalidArgumentException('Not all required parameters were passed');
        }

        $this->_objectFactory = $args['objectFactory'];

        if (false === ($this->_objectFactory instanceof Mage_Core_Model_Config)) {
            throw new InvalidArgumentException('Passed wrong instance of object factory');
        }
    }
}
