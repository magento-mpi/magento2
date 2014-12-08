<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Resource;

/**
 * TargetRule Product Index Pool
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class IndexPool
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get resource index singleton
     *
     * @param string $type
     * @param array $arguments
     * @throws \LogicException
     * @return \Magento\TargetRule\Model\Resource\Index\AbstractIndex
     */
    public function get($type, array $arguments = [])
    {
        switch ($type) {
            case \Magento\TargetRule\Model\Rule::RELATED_PRODUCTS:
                $model = 'Related';
                break;
            case \Magento\TargetRule\Model\Rule::UP_SELLS:
                $model = 'Upsell';
                break;
            case \Magento\TargetRule\Model\Rule::CROSS_SELLS:
                $model = 'Crosssell';
                break;
            default:
                throw new \LogicException($type . ' is undefined catalog product list type');
        }

        $className = 'Magento\TargetRule\Model\Resource\Index\\' . $model;
        $indexResource = $this->_objectManager->get($className, $arguments);

        if (false === $indexResource instanceof \Magento\TargetRule\Model\Resource\Index\AbstractIndex) {
            throw new \LogicException(
                $className . ' doesn\'t extend \Magento\TargetRule\Model\Resource\Index\AbstractIndex'
            );
        }

        return $indexResource;
    }
}
