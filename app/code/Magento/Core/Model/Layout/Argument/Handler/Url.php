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
 * Layout argument. Type url
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Layout_Argument_Handler_Url extends Magento_Core_Model_Layout_Argument_HandlerAbstract
{
    /**
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlModel;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_UrlInterface $urlModel
     */
    public function __construct(Magento_ObjectManager $objectManager, Magento_Core_Model_UrlInterface $urlModel)
    {
        parent::__construct($objectManager);

        $this->_urlModel = $urlModel;
    }

    /**
     * Generate url
     * @param string $value
     * @throws InvalidArgumentException
     * @return Magento_Core_Model_Abstract|boolean
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
