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
    public function __construct(Mage_Backend_Model_Widget_Grid_Parser $parser = null)
    {
        $this->_parser = ($parser)? $parser : new Mage_Backend_Model_Widget_Grid_Parser();
    }

    /**
     * Fill columns
     *
     * @param $index
     * @param $totalExpr
     */
    public function setColumn($index, $totalExpr)
    {
        $this->_columns[$index] = $totalExpr;
    }

    public function countTotals($collection)
    {
        foreach ($this->_columns as $index => $expr) {
            $this->_count($index, $expr, $collection);
        }

        return new Varien_Object($this->_totals);
    }

    /**
     * @param $index
     * @param $expr
     * @param $collection
     * @return mixed
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

    abstract protected function _countSum($index, $collection);

    abstract protected function _countAverage($index, $collection);

    protected function _countExpr($expr, $collection)
    {
        list ($t1, $t2, $t3) = $this->_parser->parseExpression($expr);

        $t1 = $this->_checkOperand($t1, $collection);
        $t2 = $this->_checkOperand($t2, $collection);

        $result = 0;
        switch ($t3) {
            case '+':
                $result = $t1 + $t2;
                break;
            case '-':
                $result = $t1 - $t2;
                break;
            case '*':
                $result = $t1 * $t2;
                break;
            case '/':
                if ($t2 == 0) {
                    $result = 0;
                } else {
                    $result = $t1 / $t2;
                }
                break;
        }

        return $result;
    }

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

    public function reset($isFullReset = false)
    {
        if ($isFullReset) {
            $this->_columns = array();
        }

        $this->_totals = array();
    }

    public function getColumns()
    {
        return $this->_columns;
    }
}
