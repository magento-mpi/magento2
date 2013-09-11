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
 * Layout argument. Type options
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Layout\Argument\Handler;

class Options extends \Magento\Core\Model\Layout\Argument\HandlerAbstract
{
    /**
     * Return option array of given option model
     * @param string $value
     * @throws \InvalidArgumentException
     * @return \Magento\Core\Model\AbstractModel|boolean
     */
    public function process($value)
    {
        /** @var $valueInstance \Magento\Core\Model\Option\ArrayInterface */
        $valueInstance = $this->_objectManager->create($value, array());
        if (false === ($valueInstance instanceof \Magento\Core\Model\Option\ArrayInterface)) {
            throw new \InvalidArgumentException('Incorrect option model');
        }
        $options = $valueInstance->toOptionArray();
        $output = array();
        foreach ($options as $value => $label) {
            $output[] = array('value' => $value, 'label' => $label);
        }
        return $output;
    }
}
