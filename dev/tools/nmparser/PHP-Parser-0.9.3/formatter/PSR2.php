<?php

/**
 * Extend the Default with PSR2 behavior.
 * @author kfries
 */

class PSR2 extends PHPParser_PrettyPrinter_Default
{
    protected $indent_level = 0;
    protected $classCount = 0;
    protected $methodCount = 0;

    protected $methodsVolatile = true;

    protected function pCommaSeparated(array $nodes) {
        $result = $this->pImplode($nodes, ', ');
        if (count($nodes) > 0 && ((count($nodes) > 1 && strlen($result) > 60) || (!strpos($result, "\n") === false))) {
            ++$this->indent_level;
            $result =  "\n" . str_repeat('    ', $this->indent_level) .
                $this->pImplode($nodes, ",\n" . str_repeat('    ', $this->indent_level)) . "\n" . str_repeat('    ', --$this->indent_level);
        }
        return $result;
    }

    public function pStmt_ClassMethod(PHPParser_Node_Stmt_ClassMethod $node)
    {
        if ($this->methodsVolatile) {
            $method_name = $node->name;
        } else {
            $method_name = $this->patchMethodName($node->name);
        }
        $method_params = $this->pCommaSeparated($node->params);
        $multiline_params = !(strpos($method_params,"\n") === false);

        $result = $this->pModifiers($node->type)
            . 'function ' . ($node->byRef ? '&' : '') . $method_name
            . '(' . $method_params . ')'
            . (null !== $node->stmts
                ? (!$multiline_params ? "\n" : ' ') . '{' . "\n" . $this->pStmts($node->stmts) . "\n" . '}'
                : ';')."\n";

        ++$this->methodCount;

        return $result;
    }

    protected function patchMethodName($method_name)
    {
        if (stristr($method_name, '_')) {
            $method_name = strtolower($method_name);

            $name_parts = explode('_', $method_name);

            $new_name = '';

            $part_count = 0;
            foreach($name_parts as $part) {
                $part = trim($part);

                if (!$part) {
                    continue;
                }

                if (!$part_count) {
                    $new_name .= $part;
                } else {
                    $new_name .= ucfirst($part);
                }

                ++$part_count;
            }

            $method_name = $new_name;
        }

        return $method_name;
    }

    public function pExpr_MethodCall(PHPParser_Node_Expr_MethodCall $node)
    {
        if ($this->methodsVolatile) {
            $method_name = $this->pObjectProperty($node->name);
        } else {
            $method_name = $this->patchMethodName($this->pObjectProperty($node->name));
        }
        return $this->pVarOrNewExpr($node->var) . '->' . $method_name
            . '(' . $this->pCommaSeparated($node->args) . ')';

    }

    public function pExpr_FuncCall(PHPParser_Node_Expr_FuncCall $node) {
        return $this->p($node->name) . '(' . $this->pCommaSeparated($node->args) . ')';//here

    }

    public function pStmt_Interface(PHPParser_Node_Stmt_Interface $node) {
        return 'interface ' . $node->name
        . (!empty($node->extends) ? ' extends ' . $this->pCommaSeparated($node->extends) : '')
        . "\n" . '{' . "\n" . $this->pStmts($node->stmts) . '}';

    }

    public function XpStmt_Class(PHPParser_Node_Stmt_Class $node) {
        return $this->pModifiers($node->type)
        . 'class ' . $node->name
        . (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '')
        . (!empty($node->implements) ? ' implements ' . $this->pCommaSeparated($node->implements) : '')
        . "\n" . '{' . "\n" . (!empty($node->stmts) ? $this->pStmts($node->stmts) : '') . '}';
    }

    public function pStmt_Class(PHPParser_Node_Stmt_Class $node)
    {
        $result = ($this->classCount ? "\n" : '').$this->pModifiers($node->type)
            . 'class ' . $node->name
            . (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '')
            . (!empty($node->implements) ? ' implements' . $this->implementsSeparated($node->implements) : '')
            . "\n" . '{' . "\n" . (!empty($node->stmts) ? $this->pStmts($node->stmts) : '') . '}';

        ++$this->classCount;

        $this->methodCount = 0;

        return $result;
    }

    protected function implementsSeparated($nodes)
    {
        if (count($nodes) > 1) {
            return "\n ".$this->pImplode($nodes, ",\n ");
        } else {
            return ' '.$this->pImplode($nodes, ', ');
        }
    }

    public function pModifiers($modifiers) {
        return ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_FINAL     ? 'final '     : '')
        . ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT  ? 'abstract '  : '')
        . ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC    ? 'public '    : '')
        . ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED ? 'protected ' : '')
        . ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE   ? 'private '   : '')
        . ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_STATIC    ? 'static '    : '');
    }

    protected function pComments(array $comments) {
        $result = '';

        foreach ($comments as $comment) {
            $c = $comment->getReformattedText();
            $result .= preg_replace("/\r\n/","\n",$c) . "\n";
        }

        return $result;
    }

    public function pStmt_Use(PHPParser_Node_Stmt_Use $node) {
        return 'use ' . $this->pCommaSeparated($node->uses) . ";\n";
    }

    public function pExpr_New(PHPParser_Node_Expr_New $node)
    {
        $instationParams = $this->pCommaSeparated($node->args);
        if (strpos($instationParams, "\n") != false) {
            return 'new ' . $this->p($node->class) . "(\n    " . $instationParams . "\n)";
        }
        return 'new ' . $this->p($node->class) . '(' . $instationParams . ')';
    }

    public function pStmt_Property(PHPParser_Node_Stmt_Property $node) {
        return $this->pModifiers($node->type) . parent::pCommaSeparated($node->props) . ';';
    }

    protected function pInfixOp($type, PHPParser_Node $leftNode, $operatorString, PHPParser_Node $rightNode) {
        list($precedence, $associativity) = $this->precedenceMap[$type];

        $result = $this->pPrec($leftNode, $precedence, $associativity, -1)
        . $operatorString
        . $this->pPrec($rightNode, $precedence, $associativity, 1);
        if (strlen($result) > 60) {
            $result = $this->pPrec($leftNode, $precedence, $associativity, -1);
            $result .= rtrim($operatorString) . "\n";
            $result .= str_repeat('    ', $this->indent_level) . $this->pPrec($rightNode, $precedence, $associativity, 1);
            return $result;
        } else {
            return $result;

        }

    }

    public function pExpr_StaticCall(PHPParser_Node_Expr_StaticCall $node) {
        $result = $this->p($node->class) . '::'
        . ($node->name instanceof PHPParser_Node_Expr
            ? ($node->name instanceof PHPParser_Node_Expr_Variable
            || $node->name instanceof PHPParser_Node_Expr_ArrayDimFetch
                ? $this->p($node->name)
                : '{' . $this->p($node->name) . '}')
            : $node->name);
        $result .= '(' . $this->pCommaSeparated($node->args) . ')' ;
        return $result;

    }

    public function pExpr_Array(PHPParser_Node_Expr_Array $node) {
        return 'array(' . $this->pCommaSeparated($node->items) . ')';
    }
}