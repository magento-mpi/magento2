<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV entity attribute exception
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Entity\Attribute;

class Exception extends \Exception
{
    /**
     * Eav entity attribute
     *
     * @var string
     */
    protected $_attributeCode;

    /**
     * Eav entity attribute part
     * attribute|backend|frontend|source
     *
     * @var string
     */
    protected $_part;

    /**
     * Set Eav entity attribute
     *
     * @param string $attribute
     * @return \Magento\Eav\Model\Entity\Attribute\Exception
     */
    public function setAttributeCode($attribute)
    {
        $this->_attributeCode = $attribute;
        return $this;
    }

    /**
     * Set Eav entity attribute type
     *
     * @param string $part
     * @return \Magento\Eav\Model\Entity\Attribute\Exception
     */
    public function setPart($part) {
        $this->_part = $part;
        return $this;
    }

    /**
     * Retrieve Eav entity attribute
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->_attributeCode;
    }

    /**
     * Retrieve Eav entity attribute part
     *
     * @return string
     */
    public function getPart()
    {
        return $this->_part;
    }
}
