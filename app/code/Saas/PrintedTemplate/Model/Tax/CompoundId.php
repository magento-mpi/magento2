<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Compound ID of tax
 * This model is used when we need to describe order in which tax was applied
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Tax_CompoundId
{
    /**
     * "After" separator for conversion to string
     *
     * @var string
     */
    const AFTER_SEPARATOR = ',';

    /**
     * "And" separator for conversion to string
     *
     * @var string
     */
    const AND_SEPARATOR = '+';

    /**
     * Internal representation
     * 5 and 3 after 10 => array(array(5, 3), 10)
     *
     * @var array
     */
    protected $_value = array();

    /**
     * Add tax applied after previous tax
     *
     * @param mixed $taxId
     * @return Saas_PrintedTemplate_Model_Tax_CompoundId Self
     */
    public function addAfter($taxId)
    {
        $this->_value[] = $taxId;

        return $this;
    }

    /**
     * Add tax applied with previous tax
     *
     * @param mixed $taxId
     * @return Saas_PrintedTemplate_Model_Tax_CompoundId Self
     */
    public function addAnd($taxId)
    {
        if (empty($this->_value)) {
            $this->_value[] = $taxId;
        } else if (is_array(end($this->_value))) {
            $this->_value[count($this->_value) - 1][] = $taxId;
        } else {
            $this->_value[count($this->_value) - 1] = array(end($this->_value), $taxId);
        }

        return $this;
    }

    /**
     * Converts ID to array
     * 5 and 3 after 10 => array(array(5, 3), 10)
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_value;
    }

    /**
     * Converts ID to string
     *
     * @see Saas_PrintedTemplate_Model_Tax_CompoundId::toString()
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Converts ID to string
     * 5 and 3 after 10 => 5+3,10
     *
     * @return string
     */
    public function toString()
    {
        $string = '';
        foreach ($this->_value as $taxId) {
            $string .= (is_array($taxId) ? join(self::AND_SEPARATOR, $taxId) : $taxId) . self::AFTER_SEPARATOR;
        }

        return trim($string, self::AFTER_SEPARATOR);
    }
}
