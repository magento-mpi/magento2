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
 * Relative length model.
 * This model is used to handle percent defined length.
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_RelativeLength
{
    /**
     * Percentage for calculation
     *
     * @var float
     */
    private $_percent;

    /**
     * Type of measurements
     *
     * @var string
     */
    const LENGTH_TYPE = 'PERCENT';

    /**
     * Creates instance
     *
     * @param float $percent Length percent
     */
    public function __construct($percent)
    {
        $this->_percent = (float)$percent;
    }

    /**
     * Returns percent value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->_percent;
    }

    /**
     * Returns base multiplied percent
     *
     * @param Zend_Measure_Length $base Base length (100%)
     * @return Zend_Measure_Length Returns new length = base * percent
     */
    public function getLength(Zend_Measure_Length $base)
    {
        return new Zend_Measure_Length($this->_percent / 100 * $base->getValue(), $base->getType());
    }

    /**
     * To be compliant with Zend_Measure_Length
     *
     * @return string
     */
    public function getType()
    {
        return self::LENGTH_TYPE;
    }

    /**
     * Converts percent to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->_percent;
    }
}
