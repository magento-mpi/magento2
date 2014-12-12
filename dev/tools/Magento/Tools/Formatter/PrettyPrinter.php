<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter;

use PHPParser_Node;
use PHPParser_Node_Expr;
use PHPParser_Node_Expr_Array;
use PHPParser_Node_Expr_ArrayDimFetch;
use PHPParser_Node_Expr_Closure;
use PHPParser_Node_Expr_ConstFetch;
use PHPParser_Node_Expr_FuncCall;
use PHPParser_Node_Expr_MethodCall;
use PHPParser_Node_Expr_New;
use PHPParser_Node_Expr_StaticCall;
use PHPParser_Node_Expr_Variable;
use PHPParser_Node_Scalar_DNumber;
use PHPParser_Node_Scalar_Encapsed;
use PHPParser_Node_Scalar_LNumber;
use PHPParser_Node_Scalar_String;
use PHPParser_Node_Stmt_Class;
use PHPParser_Node_Stmt_ClassConst;
use PHPParser_Node_Stmt_ClassMethod;
use PHPParser_Node_Stmt_Echo;
use PHPParser_Node_Stmt_Property;
use PHPParser_Node_Stmt_Use;
use PHPParser_PrettyPrinter_Default;

/**
 * Class PrettyPrinter
 *
 * In all likelihood, this class is going away, so don't really care about coupling.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PrettyPrinter extends PHPParser_PrettyPrinter_Default
{
    const EOL = "\n";

    const FORCE_MULTILINE_ARGUMENTS = 'forceMultilineArguments';

    const INDENT = '    ';

    /**
     * This method dumps the pieces of the passed in array as strings.
     *
     * @param array $encapsList
     * @return string
     */
    public function pEncapsListNoEscape(array $encapsList)
    {
        $result = '';
        foreach ($encapsList as $element) {
            if (is_string($element)) {
                // leave the string as is, so prevent indentation
                $result .= $this->preventIndent($element);
            } else {
                $result .= '{' . $this->p($element) . '}';
            }
        }
        return $result;
    }

    /**
     * This method print out an array declaration. If more than a single element in an array is present, multi-line is
     * forced.
     *
     * @param PHPParser_Node_Expr_Array $node
     * @return string
     */
    public function pExpr_Array(PHPParser_Node_Expr_Array $node)
    {
        if (count($node->items) > 1) {
            // set a flag on the first argument to force multi-line arguments
            $node->items[0]->setAttribute(self::FORCE_MULTILINE_ARGUMENTS, true);
        }
        return parent::pExpr_Array($node);
    }

    /**
     * This method prints out the closure statement. This is overridden to place a space after the use statement.
     *
     * @param PHPParser_Node_Expr_Closure $node
     * @return string
     */
    public function pExpr_Closure(PHPParser_Node_Expr_Closure $node)
    {
        $result = parent::pExpr_Closure($node);
        if (!empty($node->uses)) {
            // psr-2 says that there needs to be spaces around the use clause
            $result = preg_replace('/\) use\(/', ') use (', $result, 1);
        }
        return $result;
    }

    /**
     * This method returns constant values and defaults are ensured to return in lowercase.
     *
     * @param PHPParser_Node_Expr_ConstFetch $node
     * @return string
     */
    public function pExpr_ConstFetch(PHPParser_Node_Expr_ConstFetch $node)
    {
        $result = $this->p($node->name);
        if (strcasecmp('FALSE', $result) === 0 || strcasecmp('TRUE', $result) === 0 ||
            strcasecmp('NULL', $result) === 0) {
            $result = strtolower($result);
        }
        return $result;
    }

    /**
     * This method prints out a function call. This method deals with split parameters if they span multiple lines.
     *
     * @param PHPParser_Node_Expr_FuncCall $node
     * @return string
     */
    public function pExpr_FuncCall(PHPParser_Node_Expr_FuncCall $node)
    {
        return $this->p($node->name) . '(' . $this->getParametersForCall($node->args) . ')';
    }

    /**
     * This method prints out a method call. This method deals with split parameters if they span multiple lines.
     *
     * @param PHPParser_Node_Expr_MethodCall $node
     * @return string
     */
    public function pExpr_MethodCall(PHPParser_Node_Expr_MethodCall $node)
    {
        return $this->pVarOrNewExpr($node->var) . '->' . $this->pObjectProperty($node->name) . '(' .
            $this->getParametersForCall($node->args) . ')';
    }

    /**
     * This method prints out a new class call. This method deals with split parameters if they span multiple lines.
     *
     * @param PHPParser_Node_Expr_New $node
     * @return string
     */
    public function pExpr_New(PHPParser_Node_Expr_New $node)
    {
        return 'new ' . $this->p($node->class) . '(' . $this->getParametersForCall($node->args) . ')';
    }

    /**
     * This method prints out a static function call. This method deals with split parameters if they span multiple
     * lines.
     *
     * @param PHPParser_Node_Expr_StaticCall $node
     * @return string
     */
    public function pExpr_StaticCall(PHPParser_Node_Expr_StaticCall $node)
    {
        return $this->p($node->class) . '::' .
            ($node->name instanceof PHPParser_Node_Expr ? $node->name instanceof PHPParser_Node_Expr_Variable ||
            $node->name instanceof PHPParser_Node_Expr_ArrayDimFetch ? $this->p($node->name) : '{' .
            $this->p($node->name) . '}' : $node->name) . '(' . $this->getParametersForCall($node->args) . ')';
    }

    /**
     * This method prints the modifiers according to PSR-2, 4.5
     *
     * @param int $modifiers
     * @return string
     */
    public function pModifiers($modifiers)
    {
        return ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT ? 'abstract ' : '') .
            ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_FINAL ? 'final ' : '') .
            ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC ? 'public ' : '') .
            ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED ? 'protected ' : '') .
            ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE ? 'private ' : '') .
            ($modifiers & PHPParser_Node_Stmt_Class::MODIFIER_STATIC ? 'static ' : '');
    }

    /**
     * Pretty prints an array of statements.
     *
     * @param PHPParser_Node[] $statements Array of statements
     * @return string Pretty printed statements
     */
    public function prettyPrint(array $statements)
    {
        $this->preprocessNodes($statements);
        // resolve the statements
        $result = $this->pStmts($statements, false);
        // if the no indent token is used, escape the eol since that means it is mid stream (i.e. we want \n, not 0xA)
        $result = str_replace(self::EOL . $this->noIndentToken, self::EOL, $result);
        // remove unnecessary whitespace
        $result = preg_replace('/\\n +\\n/', self::EOL . self::EOL, $result);
        // remove blank line before close of  class
        $result = str_replace(self::EOL . self::EOL . '}', self::EOL . '}', $result);
        // remove blank lines between use statements
        do {
            $count = 0;
            $result = preg_replace(
                '~\\nuse (.*);\\n\\nuse ~',
                self::EOL . 'use $1;' . self::EOL . 'use ',
                $result, 1, $count
            );
        } while ($count > 0);
        return $result;
    }

    /**
     * Override method for handling encapsed scalars
     *
     * @param PHPParser_Node_Scalar_Encapsed $node
     * @return string
     */
    public function pScalar_Encapsed(PHPParser_Node_Scalar_Encapsed $node)
    {
        $result = '';
        $heredocCloseTag = $node->getAttribute(ParserLexer::HEREDOC_CLOSE_TAG);
        if (null !== $heredocCloseTag) {
            $result .= $this->processHeredoc($heredocCloseTag, $this->pEncapsListNoEscape($node->parts));
        } else {
            $result .= parent::pScalar_Encapsed($node);
        }
        return $result;
    }

    /**
     * This method prints out the strings found in the code. If there are special
     *
     * @param PHPParser_Node_Scalar_String $node
     * @return string
     */
    public function pScalar_String(PHPParser_Node_Scalar_String $node)
    {
        $result = $node->getAttribute(ParserLexer::ORIGINAL_VALUE);
        $heredocCloseTag = $node->getAttribute(ParserLexer::HEREDOC_CLOSE_TAG);
        if (null !== $heredocCloseTag) {
            $result = $this->processHeredoc($heredocCloseTag, $this->preventIndent($node->value));
        } elseif (null === $result) {
            // if nothing there, then use the base class version
            $result = parent::pScalar_String($node);
        }
        return $result;
    }

    /**
     * This method prints out a constant in a class. It is overridden to generate a newline after every class.
     *
     * @param PHPParser_Node_Stmt_ClassConst $node
     * @return string
     */
    public function pStmt_ClassConst(PHPParser_Node_Stmt_ClassConst $node)
    {
        return parent::pStmt_ClassConst($node) . self::EOL;
    }

    /**
     * This method prints out a method in a class. It is overridden to generate multi-line parameters if needed, and put
     * a newline after every method.
     *
     * @param PHPParser_Node_Stmt_ClassMethod $node
     * @return string
     */
    public function pStmt_ClassMethod(PHPParser_Node_Stmt_ClassMethod $node)
    {
        // predetermine parameters for the function
        $parameters = $this->getParametersForCall($node->params);
        $result = $this->pModifiers($node->type) . 'function ';
        if ($node->byRef) {
            $result .= '&';
        }
        $result .= $node->name . '(' . $parameters . ')';
        if (null !== $node->stmts) {
            // if the parameter span multiple lines, then start block on the same line; otherwise, start on new line
            if ($this->countNewlines($parameters) > 0) {
                $result .= ' ';
            } else {
                $result .= self::EOL;
            }
            $result .= '{' . self::EOL . $this->pStmts($node->stmts) . self::EOL . '}';
        } else {
            $result .= ';';
        }
        $result .= self::EOL;
        return $result;
    }

    /**
     * This method is used to print an echo statement. It is overridden to handle a special case of echoing just a
     * HEREDOC.
     *
     * @param PHPParser_Node_Stmt_Echo $node
     * @return string
     */
    public function pStmt_Echo(PHPParser_Node_Stmt_Echo $node)
    {
        return 'echo ' . trim($this->pCommaSeparated($node->exprs)) . ';';
    }

    /**
     * This method dumps a integer number. This method uses the original value, if available.
     *
     * @param PHPParser_Node_Scalar_LNumber $node
     * @return string
     */
    public function pScalar_LNumber(PHPParser_Node_Scalar_LNumber $node)
    {
        $result = $node->getAttribute(ParserLexer::ORIGINAL_VALUE);
        if (null === $result) {
            $result = parent::pScalar_LNumber($node);
        }
        return $result;
    }

    /**
     * This method dumps a decimal number. This method uses the original value, if available.
     *
     * @param PHPParser_Node_Scalar_DNumber $node
     * @return string
     */
    public function pScalar_DNumber(PHPParser_Node_Scalar_DNumber $node)
    {
        $result = $node->getAttribute(ParserLexer::ORIGINAL_VALUE);
        if (null === $result) {
            $result = parent::pScalar_DNumber($node);
        }
        return $result;
    }

    /**
     * This method prints out a property in a class. It is overridden to generate a newline after every property.
     *
     * @param PHPParser_Node_Stmt_Property $node
     * @return string
     */
    public function pStmt_Property(PHPParser_Node_Stmt_Property $node)
    {
        return parent::pStmt_Property($node) . self::EOL;
    }

    /**
     * This method is used to print out the use statements.
     *
     * @param PHPParser_Node_Stmt_Use $node
     * @return string
     */
    public function pStmt_Use(PHPParser_Node_Stmt_Use $node)
    {
        $result = '';
        // loop through and place each use on a line
        foreach ($node->uses as $use) {
            $result .= 'use ' . $this->p($use) . ';' . self::EOL;
        }
        return $result;
    }

    /**
     * This method counts the number of lines that the passed array of strings represent.
     *
     * @param string $source
     * @return int The number of new lines
     */
    protected function countNewlines($source)
    {
        return preg_match_all('~\n(?!' . $this->noIndentToken . ')~', $source);
    }

    /**
     * This method returns the nodes formatted appropriately.
     *
     * @param PHPParser_Node[] $nodes Array of Nodes to be printed
     * @return string Comma separated pretty printed parameters
     */
    protected function getParametersForCall(array $nodes)
    {
        // get the parameters normally
        $parameters = $this->pCommaSeparated($nodes);
        // if the parameters span multiple lines, then each argument needs to go on its own line
        if ($this->countNewlines($parameters) > 0) {
            // set a flag on the first argument to force multi-line arguments
            $nodes[0]->setAttribute(self::FORCE_MULTILINE_ARGUMENTS, true);
            $parameters = $this->pCommaSeparated($nodes);
        }
        return $parameters;
    }

    /**
     * This method is used to replace internal newlines in the string with a newline followed by indention.
     *
     * @param string $source
     * @return string
     */
    protected function indent($source)
    {
        return preg_replace('~\\n(?!$|' . $this->noIndentToken . ')~', self::EOL . self::INDENT, $source);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values with commas.
     *
     * @param PHPParser_Node[] $nodes Array of Nodes to be printed
     * @return string Comma separated pretty printed nodes
     */
    protected function pCommaSeparated(array $nodes)
    {
        // detect closures as arguments and force multi line if detected
        $multiline = false;
        // loop through all the nodes to get the printout
        $pNodes = [];
        foreach ($nodes as $node) {
            $pNodes[] = $this->p($node);
            // if the node a forced multiline or a heredoc, then flag it multiline
            if (null !== $node->getAttribute(self::FORCE_MULTILINE_ARGUMENTS) ||
                null !== $node->getAttribute(ParserLexer::HEREDOC_CLOSE_TAG)) {
                $multiline = true;
            }
        }
        // if the arguments span multiple lines, they need to be on a line by themselves
        if ($multiline) {
            // force first argument on a newline
            $pNodes[0] = self::EOL . $pNodes[0];
            // force the closing to be on a newline
            $pNodes[sizeof($pNodes) - 1] = $pNodes[sizeof($pNodes) - 1] . self::EOL;
            // indent entire block and change the glue to include a newline to force each argument on a newline
            $result = $this->indent(implode(',' . self::EOL, $pNodes));
        } else {
            $result = implode(', ', $pNodes);
        }
        return $result;
    }

    /**
     * This method prevents the source string front being indented by using the no indent token on all newlines found
     * in the string.
     *
     * @param string $source
     * @return string
     */
    protected function preventIndent($source)
    {
        return str_replace(self::EOL, self::EOL . $this->noIndentToken, $source);
    }

    /**
     * This method reproduces the heredoc structure.
     *
     * @param string $heredocCloseTag
     * @param string $body
     * @return string
     */
    protected function processHeredoc($heredocCloseTag, $body)
    {
        $result = '<<<' . $heredocCloseTag . self::EOL . $this->noIndentToken;
        $result .= $body;
        $result .= self::EOL . $this->noIndentToken . $heredocCloseTag;
        return $result;
    }

    /**
     * Pretty prints an array of nodes (statements) and indents them optionally.
     *
     * @param PHPParser_Node[] $nodes  Array of nodes
     * @param bool             $indent Whether to indent the printed nodes
     * @return string Pretty printed statements
     */
    protected function pStmts(array $nodes, $indent = true)
    {
        $pNodes = [];
        foreach ($nodes as $node) {
            $comments = $this->pComments($node->getAttribute('comments', []));
            // there is a special case with comments before a case statement where the comment is attached to the
            // case and not the previous block; therefore, have to deal with it with the following ugliness
            if ('Stmt_Case' == $node->getType() && strlen($comments) > 0) {
                $comments = self::INDENT . $this->indent($comments);
            }
            $pNodes[] = $comments . $this->p($node) . ($node instanceof PHPParser_Node_Expr ? ';' : '');
        }
        $result = implode(self::EOL, $pNodes);
        if ($indent) {
            $result = self::INDENT . $this->indent($result);
        }
        return $result;
    }
}
