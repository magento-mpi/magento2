<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Converter interface
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model;

interface ConverterInterface
{
    /**
     * Decode data
     *
     * @param \Magento\Model\AbstractModel $object
     * @param string $filedName
     * @return mixed
     */
    public function decode(\Magento\Model\AbstractModel $object, $filedName);

    /**
     * Encode data
     *
     * @param \Magento\Model\AbstractModel $object
     * @param string $filedName
     * @return mixed
     */
    public function encode(\Magento\Model\AbstractModel $object, $filedName);
}
