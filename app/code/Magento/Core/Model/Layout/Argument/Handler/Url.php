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
     * @param Magento_Core_Model_UrlInterface $urlModel
     */
    public function __construct(Magento_Core_Model_UrlInterface $urlModel)
    {
        $this->_urlModel = $urlModel;
    }

    /**
     * Generate url
     *
     * @param array $argument
     * @return string
     * @throws InvalidArgumentException
     */
    public function process(array $argument)
    {
        $this->_validate($argument);
        $value = $argument['value'];

        return $this->_urlModel->getUrl($value['path'], $value['params']);
    }

    /**
     * @param array $argument
     * @throws InvalidArgumentException
     */
    protected function _validate(array $argument)
    {
        parent::_validate($argument);
        $value = $argument['value'];

        if (!isset($value['path'])) {
            throw new InvalidArgumentException('Passed value has incorrect format');
        }
    }

    /**
     * @param $argument
     * @return array
     */
    protected function _getArgumentValue(Magento_Core_Model_Layout_Element $argument)
    {
        $result = array(
            'path' => (string)$argument['path'],
            'params' => array()
        );

        if (isset($argument->param)) {
            foreach ($argument->param as $param) {
                $result['params'][(string)$param['name']] = (string)$param;
            }
        }

        return $result;
    }
}