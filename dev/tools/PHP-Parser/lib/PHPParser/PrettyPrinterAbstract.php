<?php

abstract class PHPParser_PrettyPrinterAbstract
{
    protected $precedenceMap = [
        // [precedence, associativity] where for the latter -1 is %left, 0 is %nonassoc and 1 is %right
        'Expr_BitwiseNot'       => [ 1,  1],
        'Expr_PreInc'           => [ 1,  1],
        'Expr_PreDec'           => [ 1,  1],
        'Expr_PostInc'          => [ 1, -1],
        'Expr_PostDec'          => [ 1, -1],
        'Expr_UnaryPlus'        => [ 1,  1],
        'Expr_UnaryMinus'       => [ 1,  1],
        'Expr_Cast_Int'         => [ 1,  1],
        'Expr_Cast_Double'      => [ 1,  1],
        'Expr_Cast_String'      => [ 1,  1],
        'Expr_Cast_Array'       => [ 1,  1],
        'Expr_Cast_Object'      => [ 1,  1],
        'Expr_Cast_Bool'        => [ 1,  1],
        'Expr_Cast_Unset'       => [ 1,  1],
        'Expr_ErrorSuppress'    => [ 1,  1],
        'Expr_Instanceof'       => [ 2,  0],
        'Expr_BooleanNot'       => [ 3,  1],
        'Expr_Mul'              => [ 4, -1],
        'Expr_Div'              => [ 4, -1],
        'Expr_Mod'              => [ 4, -1],
        'Expr_Plus'             => [ 5, -1],
        'Expr_Minus'            => [ 5, -1],
        'Expr_Concat'           => [ 5, -1],
        'Expr_ShiftLeft'        => [ 6, -1],
        'Expr_ShiftRight'       => [ 6, -1],
        'Expr_Smaller'          => [ 7,  0],
        'Expr_SmallerOrEqual'   => [ 7,  0],
        'Expr_Greater'          => [ 7,  0],
        'Expr_GreaterOrEqual'   => [ 7,  0],
        'Expr_Equal'            => [ 8,  0],
        'Expr_NotEqual'         => [ 8,  0],
        'Expr_Identical'        => [ 8,  0],
        'Expr_NotIdentical'     => [ 8,  0],
        'Expr_BitwiseAnd'       => [ 9, -1],
        'Expr_BitwiseXor'       => [10, -1],
        'Expr_BitwiseOr'        => [11, -1],
        'Expr_BooleanAnd'       => [12, -1],
        'Expr_BooleanOr'        => [13, -1],
        'Expr_Ternary'          => [14, -1],
        // parser uses %left for assignments, but they really behave as %right
        'Expr_Assign'           => [15,  1],
        'Expr_AssignRef'        => [15,  1],
        'Expr_AssignPlus'       => [15,  1],
        'Expr_AssignMinus'      => [15,  1],
        'Expr_AssignMul'        => [15,  1],
        'Expr_AssignDiv'        => [15,  1],
        'Expr_AssignConcat'     => [15,  1],
        'Expr_AssignMod'        => [15,  1],
        'Expr_AssignBitwiseAnd' => [15,  1],
        'Expr_AssignBitwiseOr'  => [15,  1],
        'Expr_AssignBitwiseXor' => [15,  1],
        'Expr_AssignShiftLeft'  => [15,  1],
        'Expr_AssignShiftRight' => [15,  1],
        'Expr_LogicalAnd'       => [16, -1],
        'Expr_LogicalXor'       => [17, -1],
        'Expr_LogicalOr'        => [18, -1],
        'Expr_Include'          => [19, -1],
    ];

    protected $noIndentToken;
    protected $canUseSemicolonNamespaces;

    public function __construct()
    {
        $this->noIndentToken = '_NO_INDENT_' . mt_rand();
    }

    /**
     * Pretty prints an array of statements.
     *
     * @param PHPParser_Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function prettyPrint(array $stmts)
    {
        $this->preprocessNodes($stmts);

        return str_replace("\n" . $this->noIndentToken, "\n", $this->pStmts($stmts, false));
    }

    /**
     * Pretty prints an expression.
     *
     * @param PHPParser_Node_Expr $node Expression node
     *
     * @return string Pretty printed node
     */
    public function prettyPrintExpr(PHPParser_Node_Expr $node)
    {
        return str_replace("\n" . $this->noIndentToken, "\n", $this->p($node));
    }

    /**
     * Pretty prints a file of statements (includes the opening <?php tag if it is required).
     *
     * @param PHPParser_Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function prettyPrintFile(array $stmts)
    {
        $p = trim($this->prettyPrint($stmts));

        $p = preg_replace('/^\?>\n?/', '', $p, -1, $count);
        $p = preg_replace('/<\?php$/', '', $p);

        if (!$count) {
            $p = "<?php\n\n" . $p;
        }

        return $p;
    }

    /**
     * Preprocesses the top-level nodes to initialize pretty printer state.
     *
     * @param PHPParser_Node[] $nodes Array of nodes
     */
    protected function preprocessNodes(array $nodes)
    {
        /* We can use semicolon-namespaces unless there is a global namespace declaration */
        $this->canUseSemicolonNamespaces = true;
        foreach ($nodes as $node) {
            if ($node instanceof PHPParser_Node_Stmt_Namespace && null === $node->name) {
                $this->canUseSemicolonNamespaces = false;
            }
        }
    }

    /**
     * Pretty prints an array of nodes (statements) and indents them optionally.
     *
     * @param PHPParser_Node[] $nodes  Array of nodes
     * @param bool             $indent Whether to indent the printed nodes
     *
     * @return string Pretty printed statements
     */
    protected function pStmts(array $nodes, $indent = true)
    {
        $pNodes = [];
        foreach ($nodes as $node) {
            $pNodes[] = $this->pComments($node->getAttribute('comments', []))
                      . $this->p($node)
                      . ($node instanceof PHPParser_Node_Expr ? ';' : '');
        }

        if ($indent) {
            return '    ' . preg_replace(
                '~\n(?!$|' . $this->noIndentToken . ')~',
                "\n" . '    ',
                implode("\n", $pNodes)
            );
        } else {
            return implode("\n", $pNodes);
        }
    }

    /**
     * Pretty prints a node.
     *
     * @param PHPParser_Node $node Node to be pretty printed
     *
     * @return string Pretty printed node
     */
    protected function p(PHPParser_Node $node)
    {
        return $this->{'p' . $node->getType()}($node);
    }

    protected function pInfixOp($type, PHPParser_Node $leftNode, $operatorString, PHPParser_Node $rightNode)
    {
        list($precedence, $associativity) = $this->precedenceMap[$type];

        return $this->pPrec($leftNode, $precedence, $associativity, -1)
             . $operatorString
             . $this->pPrec($rightNode, $precedence, $associativity, 1);
    }

    protected function pPrefixOp($type, $operatorString, PHPParser_Node $node)
    {
        list($precedence, $associativity) = $this->precedenceMap[$type];
        return $operatorString . $this->pPrec($node, $precedence, $associativity, 1);
    }

    protected function pPostfixOp($type, PHPParser_Node $node, $operatorString)
    {
        list($precedence, $associativity) = $this->precedenceMap[$type];
        return $this->pPrec($node, $precedence, $associativity, -1) . $operatorString;
    }

    /**
     * Prints an expression node with the least amount of parentheses necessary to preserve the meaning.
     *
     * @param PHPParser_Node $node                Node to pretty print
     * @param int            $parentPrecedence    Precedence of the parent operator
     * @param int            $parentAssociativity Associativity of parent operator
     *                                            (-1 is left, 0 is nonassoc, 1 is right)
     * @param int            $childPosition       Position of the node relative to the operator
     *                                            (-1 is left, 1 is right)
     *
     * @return string The pretty printed node
     */
    protected function pPrec(PHPParser_Node $node, $parentPrecedence, $parentAssociativity, $childPosition)
    {
        $type = $node->getType();
        if (isset($this->precedenceMap[$type])) {
            $childPrecedence = $this->precedenceMap[$type][0];
            if ($childPrecedence > $parentPrecedence
                || ($parentPrecedence == $childPrecedence && $parentAssociativity != $childPosition)
            ) {
                return '(' . $this->{'p' . $type}($node) . ')';
            }
        }

        return $this->{'p' . $type}($node);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values.
     *
     * @param PHPParser_Node[] $nodes Array of Nodes to be printed
     * @param string           $glue  Character to implode with
     *
     * @return string Imploded pretty printed nodes
     */
    protected function pImplode(array $nodes, $glue = '')
    {
        $pNodes = [];
        foreach ($nodes as $node) {
            $pNodes[] = $this->p($node);
        }

        return implode($glue, $pNodes);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values with commas.
     *
     * @param PHPParser_Node[] $nodes Array of Nodes to be printed
     *
     * @return string Comma separated pretty printed nodes
     */
    protected function pCommaSeparated(array $nodes)
    {
        return $this->pImplode($nodes, ', ');
    }

    /**
     * Signals the pretty printer that a string shall not be indented.
     *
     * @param string $string Not to be indented string
     *
     * @return mixed String marked with $this->noIndentToken's.
     */
    protected function pNoIndent($string)
    {
        return str_replace("\n", "\n" . $this->noIndentToken, $string);
    }

    protected function pComments(array $comments)
    {
        $result = '';

        foreach ($comments as $comment) {
            $result .= $comment->getReformattedText() . "\n";
        }

        return $result;
    }
}
