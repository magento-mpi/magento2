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
 * Layout argument. Type url
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Layout_Argument_Handler_Url extends Mage_Core_Model_Layout_Argument_HandlerAbstract
{
    /**
     * @var Mage_Core_Model_Url
     */
    protected $_urlModel;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Url $urlModel
     */
    public function __construct(Magento_ObjectManager $objectManager, Mage_Core_Model_Url $urlModel)
    {
        parent::__construct($objectManager);

        $this->_urlModel = $urlModel;
    }

    /**
     * Generate url
     * @param string $value
     * @throws InvalidArgumentException
     * @return Mage_Core_Model_Abstract|boolean
     */
    public function process($value)
    {
        if (false === is_array($value) || (!isset($value['path']))) {
            throw new InvalidArgumentException('Passed value has incorrect format');
        }

        $params = array_key_exists('params', $value) ? $value['params'] : null;
        return $this->_urlModel->getUrl($value['path'], $params);
    }
}
