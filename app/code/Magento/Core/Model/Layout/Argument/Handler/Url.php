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
    public function __construct(\Magento\ObjectManager $objectManager, \Magento\Core\Model\UrlInterface $urlModel)
    {
        parent::__construct($objectManager);

        $this->_urlModel = $urlModel;
    }

    /**
     * Generate url
     * @param string $value
     * @throws \InvalidArgumentException
     * @return \Magento\Core\Model\AbstractModel|boolean
     */
    public function process($value)
    {
        if (false === is_array($value) || (!isset($value['path']))) {
            throw new \InvalidArgumentException('Passed value has incorrect format');
        }

        $params = array_key_exists('params', $value) ? $value['params'] : null;
        return $this->_urlModel->getUrl($value['path'], $params);
    }
}
