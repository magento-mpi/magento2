<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use PHPParser_Node;

/**
 * This class controls the mapping of the parser nodes to printer nodes.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StatementFactory
{
    /**
     * This member holds the singleton instance of this class.
     *
     * @var StatementFactory
     */
    private static $instance = null;

    /**
     * This member holds the mapping of parser nodes to statement classes.
     * @var array
     */
    protected $nodeMap = array();

    /**
     * This method returns an instance of a statement class used to process the given node.
     *
     * @param PHPParser_Node $parserNode
     */
    public function getStatement(PHPParser_Node $parserNode)
    {
        // assume the type is not recognized
        $statementName = UnknownStatement::getType();
        // if the the type is a registered type, return that class instead
        if (array_key_exists($parserNode->getType(), $this->nodeMap)) {
            $statementName = $this->nodeMap[$parserNode->getType()];
        }
        // TODO: remove this check once all statement types have been accounted for
        \PHPUnit_Framework_Assert::assertNotEquals(
            UnknownStatement::getType(),
            $statementName,
            "Unable to resolve node type of '" . $parserNode->getType() . "'"
        );
        // return an instance of the class with the parsed node as a parameter
        return new $statementName($parserNode);
    }

    /**
     * This method constructs the new factory. By default, it registers the known statement types.
     */
    protected function __construct()
    {
        $this->register('Expr_Assign', AssignmentOperator::getType());
        $this->register('Expr_AssignRef', AssignRefOperator::getType());
        $this->register('Expr_AssignPlus', AssignPlusOperator::getType());
        $this->register('Expr_AssignMinus', AssignMinusOperator::getType());
        $this->register('Expr_AssignMul', AssignMultiplyOperator::getType());
        $this->register('Expr_AssignDiv', AssignDivideOperator::getType());
        $this->register('Expr_AssignConcat', AssignConcatOperator::getType());
        $this->register('Expr_AssignMod', AssignModulusOperator::getType());
        $this->register('Expr_AssignBitwiseAnd', AssignBitwiseAndOperator::getType());
        $this->register('Expr_AssignBitwiseOr', AssignBitwiseOrOperator::getType());
        $this->register('Expr_AssignBitwiseXor', AssignBitwiseXorOperator::getType());
        $this->register('Expr_AssignShiftLeft', AssignShiftLeftOperator::getType());
        $this->register('Expr_AssignShiftRight', AssignShiftRightOperator::getType());
        $this->register('Expr_LogicalAnd', LogicalAndOperator::getType());
        $this->register('Expr_LogicalXor', LogicalXorOperator::getType());
        $this->register('Expr_LogicalOr', LogicalOrOperator::getType());
        $this->register('Expr_BitwiseNot', BitwiseNotOperator::getType());
        $this->register('Expr_PreInc', PreIncrementOperator::getType());
        $this->register('Expr_PreDec', PreDecrementOperator::getType());
        $this->register('Expr_PostInc', PostIncrementOperator::getType());
        $this->register('Expr_PostDec', PostDecrementOperator::getType());
        $this->register('Expr_Div', DivideOperator::getType());
        $this->register('Expr_Concat', ConcatOperator::getType());
        $this->register('Expr_Plus', PlusOperator::getType());
        $this->register('Expr_Mul', MultiplyOperator::getType());
        $this->register('Expr_Minus', MinusOperator::getType());
        $this->register('Stmt_Namespace', NamespaceStatement::getType());
        $this->register('Stmt_Use', UseStatement::getType());
        $this->register('Stmt_UseUse', UseReference::getType());
        $this->register('Stmt_Class', ClassStatement::getType());
        $this->register('Stmt_Interface', InterfaceStatement::getType());
        $this->register('Stmt_ClassConst', ConstantStatement::getType());
        $this->register('Const', ConstantReference::getType());
        $this->register('Stmt_Property', PropertyStatement::getType());
        $this->register('Stmt_PropertyProperty', PropertyReference::getType());
        $this->register('Stmt_ClassMethod', MethodStatement::getType());
        $this->register('Stmt_InlineHTML', InlineHtmlStatement::getType());
        $this->register('Name', ClassReference::getType());
        $this->register('Scalar_DNumber', DecimalNumberReference::getType());
        $this->register('Scalar_LNumber', IntegerNumberReference::getType());
        $this->register('Scalar_String', StringReference::getType());
        $this->register('Stmt_Return', ReturnStatement::getType());
        $this->register('Expr_Variable', ExpressionReference::getType());
        $this->register('Expr_MethodCall', MethodCall::getType());
        $this->register('Stmt_Echo', EchoStatement::getType());
        $this->register('Param', ParameterReference::getType());
        $this->register('Name_FullyQualified', ClassReference::getType());
        $this->register('Expr_Array', ArrayReference::getType());
        $this->register('Expr_ArrayItem', ArrayItemReference::getType());
        $this->register('Stmt_Foreach', ForEachStatement::getType());
        $this->register('Stmt_For', ForStatement::getType());
        $this->register('Stmt_If', IfStatement::getType());
        $this->register('Stmt_ElseIf', ElseIfStatement::getType());
        $this->register('Stmt_Else', ElseStatement::getType());
    }

    /**
     * This method registers the given parser node type to the named statement.
     *
     * @param string $parserNodeName Contains the name corresponding to the type of parser node
     * @param string $statement Contains the name of the class used to process the parser node
     */
    protected function register($parserNodeName, $statement)
    {
        $this->nodeMap[$parserNodeName] = $statement;
    }

    /**
     * This method returns the singleton instance of the factory.
     *
     * @return StatementFactory
     */
    public static function getInstance()
    {
        // if the singleton object has not been allocated, then allocate it
        if (null === self::$instance) {
            self::$instance = new StatementFactory();
        }
        return self::$instance;
    }
}
