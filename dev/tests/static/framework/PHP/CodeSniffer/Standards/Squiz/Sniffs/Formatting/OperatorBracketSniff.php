<?php
/**
 * Squiz_Sniffs_Formatting_OperationBracketSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_Formatting_OperationBracketSniff.
 *
 * Tests that all arithmetic operations are bracketed.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.5.2
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Formatting_OperatorBracketSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$operators;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($phpcsFile->tokenizerType === 'JS' && $tokens[$stackPtr]['code'] === T_PLUS) {
            // JavaScript uses the plus operator for string concatenation as well
            // so we cannot accurately determine if it is a string concat or addition.
            // So just ignore it.
            return;
        }

        // If the & is a reference, then we don't want to check for brackets.
        if ($tokens[$stackPtr]['code'] === T_BITWISE_AND && $phpcsFile->isReference($stackPtr) === true) {
            return;
        }

        // There is one instance where brackets aren't needed, which involves
        // the minus sign being used to assign a negative number to a variable.
        if ($tokens[$stackPtr]['code'] === T_MINUS) {
            // Check to see if we are trying to return -n.
            $prev = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($tokens[$prev]['code'] === T_RETURN) {
                return;
            }

            $number = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($tokens[$number]['code'] === T_LNUMBER || $tokens[$number]['code'] === T_DNUMBER) {
                $previous = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
                if ($previous !== false) {
                    $isAssignment = in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens);
                    $isEquality   = in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$equalityTokens);
                    $isComparison = in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$comparisonTokens);
                    if ($isAssignment === true || $isEquality === true || $isComparison === true) {
                        // This is a negative assignment or comparison.
                        // We need to check that the minus and the number are
                        // adjacent.
                        if (($number - $stackPtr) !== 1) {
                            $error = 'No space allowed between minus sign and number';
                            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterMinus');
                        }

                        return;
                    }
                }
            }
        }//end if

        $lastBracket = false;
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            $parenthesis = array_reverse($tokens[$stackPtr]['nested_parenthesis'], true);
            foreach ($parenthesis as $bracket => $endBracket) {
                $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($bracket - 1), null, true);
                $prevCode  = $tokens[$prevToken]['code'];

                if ($prevCode === T_ISSET) {
                    // This operation is inside an isset() call, but has
                    // no bracket of it's own.
                    break;
                }

                if ($prevCode === T_STRING || $prevCode === T_SWITCH) {
                    // We allow very simple operations to not be bracketed.
                    // For example, ceil($one / $two).
                    $allowed = array(
                                T_VARIABLE,
                                T_LNUMBER,
                                T_DNUMBER,
                                T_STRING,
                                T_WHITESPACE,
                                T_THIS,
                                T_OBJECT_OPERATOR,
                                T_OPEN_SQUARE_BRACKET,
                                T_CLOSE_SQUARE_BRACKET,
                                T_MODULUS,
                               );

                    for ($prev = ($stackPtr - 1); $prev > $bracket; $prev--) {
                        if (in_array($tokens[$prev]['code'], $allowed) === true) {
                            continue;
                        }

                        if ($tokens[$prev]['code'] === T_CLOSE_PARENTHESIS) {
                            $prev = $tokens[$prev]['parenthesis_opener'];
                        } else {
                            break;
                        }
                    }

                    if ($prev !== $bracket) {
                        break;
                    }

                    for ($next = ($stackPtr + 1); $next < $endBracket; $next++) {
                        if (in_array($tokens[$next]['code'], $allowed) === true) {
                            continue;
                        }

                        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
                            $next = $tokens[$next]['parenthesis_closer'];
                        } else {
                            break;
                        }
                    }

                    if ($next !== $endBracket) {
                        break;
                    }
                }//end if

                if (in_array($prevCode, PHP_CodeSniffer_Tokens::$scopeOpeners) === true) {
                    // This operation is inside a control structure like FOREACH
                    // or IF, but has no bracket of it's own.
                    // The only control structure allowed to do this is SWITCH.
                    if ($prevCode !== T_SWITCH) {
                        break;
                    }
                }

                if ($prevCode === T_OPEN_PARENTHESIS) {
                    // These are two open parenthesis in a row. If the current
                    // one doesn't enclose the operator, go to the previous one.
                    if ($endBracket < $stackPtr) {
                        continue;
                    }
                }

                $lastBracket = $bracket;
                break;
            }//end foreach
        }//end if

        if ($lastBracket === false) {
            // It is not in a bracketed statement at all.
            $previousToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true, null, true);
            if ($previousToken !== false) {
                // A list of tokens that indicate that the token is not
                // part of an arithmetic operation.
                $invalidTokens = array(
                                  T_COMMA,
                                  T_COLON,
                                  T_OPEN_PARENTHESIS,
                                  T_OPEN_SQUARE_BRACKET,
                                  T_CASE,
                                 );

                if (in_array($tokens[$previousToken]['code'], $invalidTokens) === false) {
                    $error = 'Arithmetic operation must be bracketed';
                    $phpcsFile->addError($error, $stackPtr, 'MissingBrackets');
                }

                return;
            }
        } else if ($tokens[$lastBracket]['parenthesis_closer'] < $stackPtr) {
            // There are a set of brackets in front of it that don't include it.
            $error = 'Arithmetic operation must be bracketed';
            $phpcsFile->addError($error, $stackPtr, 'MissingBrackets');
            return;
        } else {
            // We are enclosed in a set of bracket, so the last thing to
            // check is that we are not also enclosed in square brackets
            // like this: ($array[$index + 1]), which is invalid.
            $brackets = array(
                         T_OPEN_SQUARE_BRACKET,
                         T_CLOSE_SQUARE_BRACKET,
                        );

            $squareBracket = $phpcsFile->findPrevious($brackets, ($stackPtr - 1), $lastBracket);
            if ($squareBracket !== false && $tokens[$squareBracket]['code'] === T_OPEN_SQUARE_BRACKET) {
                $closeSquareBracket = $phpcsFile->findNext($brackets, ($stackPtr + 1));
                if ($closeSquareBracket !== false && $tokens[$closeSquareBracket]['code'] === T_CLOSE_SQUARE_BRACKET) {
                    $error = 'Arithmetic operation must be bracketed';
                    $phpcsFile->addError($error, $stackPtr, 'MissingBrackets');
                }
            }

            return;
        }//end if

        $lastAssignment = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$assignmentTokens, $stackPtr, null, false, null, true);
        if ($lastAssignment !== false && $lastAssignment > $lastBracket) {
            $error = 'Arithmetic operation must be bracketed';
            $phpcsFile->addError($error, $stackPtr, 'MissingBrackets');
        }

    }//end process()


}//end class

?>
