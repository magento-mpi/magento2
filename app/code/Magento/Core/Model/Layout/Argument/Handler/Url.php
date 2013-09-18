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
namespace Magento\Core\Model\Layout\Argument\Handler;

class Url extends \Magento\Core\Model\Layout\Argument\HandlerAbstract
{
    /**
     * @var \Magento\Core\Model\UrlInterface
     */
    protected $_urlModel;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\UrlInterface $urlModel
     */
    public function __construct(\Magento\Core\Model\UrlInterface  $urlModel)
    {
        $this->_urlModel = $urlModel;
    }

    /**
     * Generate url
     *
     * @param array $argument
     * @return string
     * @throws \InvalidArgumentException
     */
    public function process(array $argument)
    {
        $this->_validate($argument);
        $value = $argument['value'];

        return $this->_urlModel->getUrl($value['path'], $value['params']);
    }

    /**
     * @param array $argument
     * @throws \InvalidArgumentException
     */
    protected function _validate(array $argument)
    {
        parent::_validate($argument);
        $value = $argument['value'];

        if (!isset($value['path'])) {
            throw new \InvalidArgumentException(
                'Passed value has incorrect format. ' . $this->_getArgumentInfo($argument)
            );
        }
    }

    /**
     * @param $argument
     * @return array
     */
    protected function _getArgumentValue(\Magento\Core\Model\Layout\Element $argument)
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
