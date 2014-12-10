<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Converter interface
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model;

interface ConverterInterface
{
    /**
     * Decode data
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param string $filedName
     * @return mixed
     */
    public function decode(\Magento\Framework\Model\AbstractModel $object, $filedName);

    /**
     * Encode data
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param string $filedName
     * @return mixed
     */
    public function encode(\Magento\Framework\Model\AbstractModel $object, $filedName);
}
