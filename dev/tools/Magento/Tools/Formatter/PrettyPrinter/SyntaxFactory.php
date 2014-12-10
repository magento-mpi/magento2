<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignBitwiseAndOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignBitwiseOrOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignBitwiseXorOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignConcatOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignDivideOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignmentOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignMinusOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignModulusOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignMultiplyOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignPlusOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignRefOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignShiftLeftOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\AssignShiftRightOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\BitwiseAndOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\BitwiseNotOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\BitwiseOrOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\BitwiseXorOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\BooleanAndOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\BooleanNotOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\BooleanOrOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\CastArrayOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\CastBoolOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\CastDoubleOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\CastIntOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\CastObjectOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\CastStringOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\CastUnsetOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\ConcatOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\DivideOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\EqualOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\ErrorSuppressOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\GreaterOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\GreaterOrEqualOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\IdenticalOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\InstanceofOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\LogicalAndOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\LogicalOrOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\LogicalXorOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\MinusOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\ModulusOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\MultiplyOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\NotEqualOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\NotIdenticalOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\PlusOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\PostDecrementOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\PostIncrementOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\PreDecrementOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\PreIncrementOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\ShiftLeftOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\ShiftRightOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\SmallerOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\SmallerOrEqualOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\TernaryOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\UnaryMinusOperator;
use Magento\Tools\Formatter\PrettyPrinter\Operator\UnaryPlusOperator;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ArgumentReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ArrayIndexedReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ArrayItemReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ArrayReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ClassConstant;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ClassConstantReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ClassConstantScalarReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ClassReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\CloneReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ClosureReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ClosureUseReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ConstantReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\DecimalNumberReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\DirConstReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\EmptyReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\EncapsedReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\EvalReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ExitReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ExpressionReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\FileConstantReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\FunctionCall;
use Magento\Tools\Formatter\PrettyPrinter\Reference\FunctionConstantReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\IncludeReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\IntegerNumberReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\IssetReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\LineConstantReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ListReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\MethodCall;
use Magento\Tools\Formatter\PrettyPrinter\Reference\MethodConstantReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\NamespaceConstantReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\NewReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ParameterReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\PrintReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\PropertyCall;
use Magento\Tools\Formatter\PrettyPrinter\Reference\PropertyReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\ShellExecReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\StaticCallReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\StaticPropertyReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\StaticVariableReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\StringReference;
use Magento\Tools\Formatter\PrettyPrinter\Reference\UseReference;
use Magento\Tools\Formatter\PrettyPrinter\Statement\AbstractStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\BreakStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\CaseStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\CatchStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ClassStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ConstantStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ContinueStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\DoStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\EchoStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ElseIfStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ElseStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ForEachStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ForStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\FunctionStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\GlobalVariableStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\IfStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\InlineHtmlStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\InterfaceStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\MethodStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\NamespaceStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\PropertyStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ReturnStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\StaticVariableStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\SwitchStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\ThrowStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\TryCatchStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\UnknownStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\UnsetStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\UseStatement;
use Magento\Tools\Formatter\PrettyPrinter\Statement\WhileStatement;
use PHPParser_Node;

/**
 * This class controls the mapping of the parser nodes to printer nodes.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SyntaxFactory
{
    /**
     * This member holds the singleton instance of this class.
     *
     * @var SyntaxFactory
     */
    private static $instance = null;

    /**
     * This member holds the mapping of parser nodes to statement classes.
     *
     * @var array
     */
    protected $nodeMap = [];

    /**
     * This method returns an instance of a statement class used to process the given node.
     *
     * @param PHPParser_Node $parserNode
     * @return AbstractStatement
     */
    public function getStatement(PHPParser_Node $parserNode)
    {
        // assume the type is not recognized
        $statementName = UnknownStatement::getType();
        // if the the type is a registered type, return that class instead
        if (array_key_exists($parserNode->getType(), $this->nodeMap)) {
            $statementName = $this->nodeMap[$parserNode->getType()];
        } else {
            // TODO: remove this check once all statement types have been accounted for
            //        \PHPUnit_Framework_Assert::assertNotEquals(
            //            UnknownStatement::getType(),
            //            $statementName,
            //            "Unable to resolve node type of '" . $parserNode->getType() . "'"
            //        );
            //        throw new Exception("Unable to resolve node type of '" . $parserNode->getType() . "'");
            echo "Unable to resolve node type of '" . $parserNode->getType() . "'\n";
        }

        // return an instance of the class with the parsed node as a parameter
        return new $statementName($parserNode);
    }

    /**
     * This method constructs the new factory. By default, it registers the known statement types.
     */
    protected function __construct()
    {
        $this->register('Const', ClassConstant::getType());
        $this->register('Name', ClassReference::getType());
        $this->register('Param', ParameterReference::getType());
        $this->register('Name_FullyQualified', ClassReference::getType());
        $this->register('Arg', ArgumentReference::getType());
        $this->registerExprs();
        $this->registerScalars();
        $this->registerStmts();
    }

    /**
     * This method registers the given parser node type to the named statement.
     *
     * @param string $parserNodeName Contains the name corresponding to the type of parser node
     * @param string $statement Contains the name of the class used to process the parser node
     * @return void
     */
    protected function register($parserNodeName, $statement)
    {
        $this->nodeMap[$parserNodeName] = $statement;
    }

    /**
     * This method registers the expression types.
     *
     * @return void
     */
    protected function registerExprs()
    {
        $this->register('Expr_Isset', IssetReference::getType());
        $this->register('Expr_Include', IncludeReference::getType());
        $this->register('Expr_Instanceof', InstanceofOperator::getType());
        $this->register('Expr_Cast_Int', CastIntOperator::getType());
        $this->register('Expr_Cast_Double', CastDoubleOperator::getType());
        $this->register('Expr_Cast_String', CastStringOperator::getType());
        $this->register('Expr_Cast_Array', CastArrayOperator::getType());
        $this->register('Expr_Cast_Object', CastObjectOperator::getType());
        $this->register('Expr_Cast_Bool', CastBoolOperator::getType());
        $this->register('Expr_Cast_Unset', CastUnsetOperator::getType());
        $this->register('Expr_Ternary', TernaryOperator::getType());
        $this->register('Expr_Greater', GreaterOperator::getType());
        $this->register('Expr_GreaterOrEqual', GreaterOrEqualOperator::getType());
        $this->register('Expr_Smaller', SmallerOperator::getType());
        $this->register('Expr_SmallerOrEqual', SmallerOrEqualOperator::getType());
        $this->register('Expr_Equal', EqualOperator::getType());
        $this->register('Expr_NotEqual', NotEqualOperator::getType());
        $this->register('Expr_Identical', IdenticalOperator::getType());
        $this->register('Expr_NotIdentical', NotIdenticalOperator::getType());
        $this->register('Expr_BooleanNot', BooleanNotOperator::getType());
        $this->register('Expr_BooleanAnd', BooleanAndOperator::getType());
        $this->register('Expr_BooleanOr', BooleanOrOperator::getType());
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
        $this->register('Expr_BitwiseAnd', BitwiseAndOperator::getType());
        $this->register('Expr_BitwiseOr', BitwiseOrOperator::getType());
        $this->register('Expr_BitwiseXor', BitwiseXorOperator::getType());
        $this->register('Expr_ErrorSuppress', ErrorSuppressOperator::getType());
        $this->register('Expr_PreInc', PreIncrementOperator::getType());
        $this->register('Expr_PreDec', PreDecrementOperator::getType());
        $this->register('Expr_PostInc', PostIncrementOperator::getType());
        $this->register('Expr_PostDec', PostDecrementOperator::getType());
        $this->register('Expr_UnaryPlus', UnaryPlusOperator::getType());
        $this->register('Expr_UnaryMinus', UnaryMinusOperator::getType());
        $this->register('Expr_Div', DivideOperator::getType());
        $this->register('Expr_Concat', ConcatOperator::getType());
        $this->register('Expr_Plus', PlusOperator::getType());
        $this->register('Expr_Mul', MultiplyOperator::getType());
        $this->register('Expr_Mod', ModulusOperator::getType());
        $this->register('Expr_Minus', MinusOperator::getType());
        $this->register('Expr_ShiftLeft', ShiftLeftOperator::getType());
        $this->register('Expr_ShiftRight', ShiftRightOperator::getType());
        $this->register('Expr_ConstFetch', ConstantReference::getType());
        $this->register('Expr_Variable', ExpressionReference::getType());
        $this->register('Expr_MethodCall', MethodCall::getType());
        $this->register('Expr_Array', ArrayReference::getType());
        $this->register('Expr_ArrayItem', ArrayItemReference::getType());
        $this->register('Expr_ClassConstFetch', ClassConstantReference::getType());
        $this->register('Expr_StaticCall', StaticCallReference::getType());
        $this->register('Expr_PropertyFetch', PropertyCall::getType());
        $this->register('Expr_FuncCall', FunctionCall::getType());
        $this->register('Expr_ArrayDimFetch', ArrayIndexedReference::getType());
        $this->register('Expr_New', NewReference::getType());
        $this->register('Expr_Empty', EmptyReference::getType());
        $this->register('Expr_Eval', EvalReference::getType());
        $this->register('Expr_Exit', ExitReference::getType());
        $this->register('Expr_Clone', CloneReference::getType());
        $this->register('Expr_Print', PrintReference::getType());
        $this->register('Expr_ShellExec', ShellExecReference::getType());
        $this->register('Expr_StaticPropertyFetch', StaticPropertyReference::getType());
        $this->register('Expr_List', ListReference::getType());
        $this->register('Expr_Closure', ClosureReference::getType());
        $this->register('Expr_ClosureUse', ClosureUseReference::getType());
    }

    /**
     * This method registers the scalar types.
     *
     * @return void
     */
    protected function registerScalars()
    {
        $this->register('Scalar_DirConst', DirConstReference::getType());
        $this->register('Scalar_DNumber', DecimalNumberReference::getType());
        $this->register('Scalar_LNumber', IntegerNumberReference::getType());
        $this->register('Scalar_String', StringReference::getType());
        $this->register('Scalar_Encapsed', EncapsedReference::getType());
        $this->register('Scalar_MethodConst', MethodConstantReference::getType());
        $this->register('Scalar_FileConst', FileConstantReference::getType());
        $this->register('Scalar_NSConst', NamespaceConstantReference::getType());
        $this->register('Scalar_LineConst', LineConstantReference::getType());
        $this->register('Scalar_FuncConst', FunctionConstantReference::getType());
        $this->register('Scalar_ClassConst', ClassConstantScalarReference::getType());
    }

    /**
     * This method registers the statements.
     *
     * @return void
     */
    protected function registerStmts()
    {
        $this->register('Stmt_Namespace', NamespaceStatement::getType());
        $this->register('Stmt_Unset', UnsetStatement::getType());
        $this->register('Stmt_Use', UseStatement::getType());
        $this->register('Stmt_UseUse', UseReference::getType());
        $this->register('Stmt_Class', ClassStatement::getType());
        $this->register('Stmt_Interface', InterfaceStatement::getType());
        $this->register('Stmt_ClassConst', ConstantStatement::getType());
        $this->register('Stmt_Property', PropertyStatement::getType());
        $this->register('Stmt_PropertyProperty', PropertyReference::getType());
        $this->register('Stmt_ClassMethod', MethodStatement::getType());
        $this->register('Stmt_InlineHTML', InlineHtmlStatement::getType());
        $this->register('Stmt_Return', ReturnStatement::getType());
        $this->register('Stmt_Echo', EchoStatement::getType());
        $this->register('Stmt_Foreach', ForEachStatement::getType());
        $this->register('Stmt_For', ForStatement::getType());
        $this->register('Stmt_While', WhileStatement::getType());
        $this->register('Stmt_Do', DoStatement::getType());
        $this->register('Stmt_If', IfStatement::getType());
        $this->register('Stmt_ElseIf', ElseIfStatement::getType());
        $this->register('Stmt_Else', ElseStatement::getType());
        $this->register('Stmt_Function', FunctionStatement::getType());
        $this->register('Stmt_Break', BreakStatement::getType());
        $this->register('Stmt_Continue', ContinueStatement::getType());
        $this->register('Stmt_Switch', SwitchStatement::getType());
        $this->register('Stmt_Case', CaseStatement::getType());
        $this->register('Stmt_Throw', ThrowStatement::getType());
        $this->register('Stmt_TryCatch', TryCatchStatement::getType());
        $this->register('Stmt_Catch', CatchStatement::getType());
        $this->register('Stmt_Static', StaticVariableStatement::getType());
        $this->register('Stmt_Global', GlobalVariableStatement::getType());
        $this->register('Stmt_StaticVar', StaticVariableReference::getType());
    }

    /**
     * This method returns the singleton instance of the factory.
     *
     * @return SyntaxFactory
     */
    public static function getInstance()
    {
        // if the singleton object has not been allocated, then allocate it
        if (null === self::$instance) {
            self::$instance = new SyntaxFactory();
        }
        return self::$instance;
    }
}
