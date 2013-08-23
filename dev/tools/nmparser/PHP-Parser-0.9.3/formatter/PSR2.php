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

    protected function pCommaSeparatedMethodParams(array $nodes) {
        if(count($nodes) > 2) {
            return "\n" . str_repeat('    ', $this->indent_level) .
            $this->pImplode($nodes, ",\n" . str_repeat('    ', $this->indent_level)) . "\n";
        } else {
            return $this->pImplode($nodes, ', ');
        }
    }

    public function pStmt_ClassMethod(PHPParser_Node_Stmt_ClassMethod $node)
    {
        if ($this->methodsVolatile) {
            $method_name = $node->name;
        } else {
            $method_name = $this->patchMethodName($node->name);
        }

        $method_params = $this->pCommaSeparatedMethodParams($node->params);

        if (isset($method_params{0}) && $method_params{0} == "\n") {
            $multiline_params = true;
        } else {
            $multiline_params = false;
        }

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
        ++$this->indent_level;
        $method_params = $this->pCommaSeparatedMethodParams($node->args);
        --$this->indent_level;
        return $this->pVarOrNewExpr($node->var) . '->' . $method_name
        . '(' . $method_params . ')';
    }

    public function pStmt_Interface(PHPParser_Node_Stmt_Interface $node) {
        return 'interface ' . $node->name
        . (!empty($node->extends) ? ' extends ' . $this->pCommaSeparated($node->extends) : '')
        . "\n" . '{' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';

    }

    public function XpStmt_Class(PHPParser_Node_Stmt_Class $node) {
        return $this->pModifiers($node->type)
        . 'class ' . $node->name
        . (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '')
        . (!empty($node->implements) ? ' implements ' . $this->pCommaSeparated($node->implements) : '')
        . "\n" . '{' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_Class(PHPParser_Node_Stmt_Class $node)
    {
        $result = ($this->classCount ? "\n" : '').$this->pModifiers($node->type)
            . 'class ' . $node->name
            . (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '')
            . (!empty($node->implements) ? ' implements' . $this->implementsSeparated($node->implements) : '')
            . "\n" . '{' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';

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
        . ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC    ? 'public '    : '')
        . ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED ? 'protected ' : '')
        . ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE   ? 'private '   : '')
        . ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_STATIC    ? 'static '    : '')
        . ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT  ? 'abstract '  : '');
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

}