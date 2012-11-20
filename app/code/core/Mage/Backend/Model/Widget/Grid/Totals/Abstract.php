<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Mage_Backend_Model_Widget_Grid_Totals_Abstract
    implements Mage_Backend_Model_Widget_Grid_Totals_Interface
{
    /**
     * List of columns should be proceed with expression
     * 'key' => column index
     * 'value' => column expression
     *
     * @var array
     */
    protected $_columns = array();

    /**
     * Array of totals based on columns index
     * 'key' => column index
     * 'value' => counted total
     *
     * @var array
     */
    protected $_totals = array();

    /**
     * Parser for expressions like operand operation operand
     *
     * @var Mage_Backend_Model_Widget_Grid_Parser
     */
    protected $_parser;

    /**
     * @param Mage_Backend_Model_Widget_Grid_Parser $parser
     */
    public function __construct(Mage_Backend_Model_Widget_Grid_Parser $parser)
    {
        $this->_parser = $parser;
    }

    /**
     * Fill columns
     *
     * @param $index
     * @param $totalExpr
     * @return Mage_Backend_Model_Widget_Grid_Totals_Abstract
     */
    public function setColumn($index, $totalExpr)
    {
        $this->_columns[$index] = $totalExpr;
        return $this;
    }

    /**
     * Count totals for all columns set
     *
     * @param $collection Varien_Data_Collection
     * @return Varien_Object
     */
    public function countTotals($collection)
    {
        foreach ($this->_columns as $index => $expr) {
            $this->_count($index, $expr, $collection);
        }

        return $this->getTotals();
    }

    /**
     * Get totals as object
     *
     * @return Varien_Object
     */
    public function getTotals()
    {
        return new Varien_Object($this->_totals);
    }

    /**
     * Count collection column sum based on column index and expression
     *
     * @param $index
     * @param $expr
     * @param $collection
     * @return float|int
     */
    protected function _count($index, $expr, $collection)
    {
        switch ($expr) {
            case 'sum':
                $result = $this->_countSum($index, $collection);
                break;
            case 'avg':
                $result = $this->_countAverage($index, $collection);
                break;
            default:
                $result = $this->_countExpr($expr, $collection);
                break;
        }
        $this->_totals[$index] = $result;

        return $result;
    }

    /**
     * Count collection column sum based on column index
     *
     * @abstract
     * @param $index
     * @param $collection
     * @return float|int
     */
    abstract protected function _countSum($index, $collection);

    /**
     * Count collection column average based on column index
     *
     * @abstract
     * @param $index
     * @param $collection
     * @return float|int
     */
    abstract protected function _countAverage($index, $collection);

    /**
     * Return counted expression accorded parsed string
     *
     * @param $expr
     * @param $collection Varien_Data_Collection
     * @return float|int
     */
    protected function _countExpr($expr, $collection)
    {
        list ($firstOperand, $secondOperand, $operation) = $this->_parser->parseExpression($expr);

        $firstOperand = $this->_checkOperand($firstOperand, $collection);
        $secondOperand = $this->_checkOperand($secondOperand, $collection);

        $result = 0;
        switch ($operation) {
            case '+':
                $result = $firstOperand + $secondOperand;
                break;
            case '-':
                $result = $firstOperand - $secondOperand;
                break;
            case '*':
                $result = $firstOperand * $secondOperand;
                break;
            case '/':
                $result = ($secondOperand) ? $firstOperand / $secondOperand : $secondOperand;
                break;
        }

        return $result;
    }

    /**
     * Check operand is numeric or has already counted
     *
     * @param $operand
     * @param $collection
     * @return float|int
     */
    protected function _checkOperand($operand, $collection)
    {
        if (!is_numeric($operand)) {
            if (isset($this->_totals[$operand])) {
                $operand = $this->_totals[$operand];
            } else {
                $operand = $this->_count($operand, $this->_columns[$operand], $collection);
            }
        }
        return $operand;
    }

    /**
     * Reset totals and columns set
     *
     * @param bool $isFullReset
     */
    public function reset($isFullReset = false)
    {
        if ($isFullReset) {
            $this->_columns = array();
        }

        $this->_totals = array();
    }

    /**
     * Return columns set
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->_columns;
    }
}
