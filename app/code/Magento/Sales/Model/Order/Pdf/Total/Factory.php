<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Pdf\Total;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Default total model
     *
     * @var string
     */
    protected $_defaultTotalModel = 'Magento\Sales\Model\Order\Pdf\Total\DefaultTotal';

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create instance of a total model
     *
     * @param string|null $class
     * @param array $arguments
     * @return \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
     * @throws \Magento\Framework\Model\Exception
     */
    public function create($class = null, $arguments = array())
    {
        $class = $class ?: $this->_defaultTotalModel;
        if (!is_a($class, 'Magento\Sales\Model\Order\Pdf\Total\DefaultTotal', true)) {
            throw new \Magento\Framework\Model\Exception(
                __(
                    "The PDF total model {$class} must be or extend " .
                    "\\Magento\\Sales\\Model\\Order\\Pdf\\Total\\DefaultTotal."
                )
            );
        }
        return $this->_objectManager->create($class, $arguments);
    }
}
