<?php
/**
 * Parses and verifies the doc comments for functions.
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
 *
 * @SuppressWarnings(PHPMD)
 */
include_once 'Helper.php';
class Magento_Sniffs_Annotations_RequireAnnotatedMethodsSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * The name of the method that we are currently processing.
     *
     * @var string
     */
    private $_methodName = '';

    /**
     * The position in the stack where the function token was found.
     *
     * @var int
     */
    private $_functionToken = null;

    /**
     * The position in the stack where the class token was found.
     *
     * @var int
     */
    private $_classToken = null;

    /**
     * The index of the current tag we are processing.
     *
     * @var int
     */
    private $_tagIndex = 0;

    /**
     * The function comment parser for the current method.
     *
     * @var PHP_CodeSniffer_Comment_Parser_FunctionCommentParser
     */
    protected $commentParser = null;

    /**
     * The sniff helper for stuff shared between the annotations sniffs
     *
     * @var Magento_Sniffs_Annotations_Helper
     */
    protected $helper = null;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FUNCTION);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->helper = new Magento_Sniffs_Annotations_Helper($phpcsFile);

        $tokens = $phpcsFile->getTokens();

        $find = array(T_COMMENT, T_DOC_COMMENT, T_CLASS, T_FUNCTION, T_OPEN_TAG);

        $commentEnd = $phpcsFile->findPrevious($find, $stackPtr - 1);

        if ($commentEnd === false) {
            return;
        }

        // If the token that we found was a class or a function, then this
        // function has no doc comment.
        $code = $tokens[$commentEnd]['code'];

        if ($code === T_COMMENT) {
            // The function might actually be missing a comment, and this last comment
            // found is just commenting a bit of code on a line. So if it is not the
            // only thing on the line, assume we found nothing.
            $prevContent = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $commentEnd);
            if ($tokens[$commentEnd]['line'] === $tokens[$commentEnd]['line']) {
                $error = 'Missing function doc comment';
                $this->helper->addMessage($error, $stackPtr, Magento_Sniffs_Annotations_Helper::MISSING);
            } else {
                $error = 'You must use "/**" style comments for a function comment';
                $this->helper->addMessage($error, $stackPtr, Magento_Sniffs_Annotations_Helper::WRONG_STYLE);
            }
            return;
        } elseif ($code !== T_DOC_COMMENT) {
            $error = 'Missing function doc comment';
            $this->helper->addMessage($error, $stackPtr, Magento_Sniffs_Annotations_Helper::MISSING);
            return;
        } elseif (trim($tokens[$commentEnd]['content']) !== '*/') {
            $error = 'You must use "*/" to end a function comment; found "%s"';
            $this->helper->addMessage(
                $error,
                $commentEnd,
                Magento_Sniffs_Annotations_Helper::WRONG_END,
                array(trim($tokens[$commentEnd]['content']))
            );
            return;
        }

        // If there is any code between the function keyword and the doc block
        // then the doc block is not for us.
        $ignore = PHP_CodeSniffer_Tokens::$scopeModifiers;
        $ignore[] = T_STATIC;
        $ignore[] = T_WHITESPACE;
        $ignore[] = T_ABSTRACT;
        $ignore[] = T_FINAL;
        $prevToken = $phpcsFile->findPrevious($ignore, $stackPtr - 1, null, true);
        if ($prevToken !== $commentEnd) {
            $this->helper->addMessage(
                'Missing function doc comment',
                $stackPtr,
                Magento_Sniffs_Annotations_Helper::MISSING
            );
            return;
        }

        $this->_functionToken = $stackPtr;

        $this->_classToken = null;
        foreach ($tokens[$stackPtr]['conditions'] as $condPtr => $condition) {
            if ($condition === T_CLASS || $condition === T_INTERFACE) {
                $this->_classToken = $condPtr;
                break;
            }
        }

        // Find the first doc comment.
        $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT, $commentEnd - 1, null, true) + 1;
        $commentString = $phpcsFile->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);
        $this->_methodName = $phpcsFile->getDeclarationName($stackPtr);

        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_FunctionCommentParser($commentString, $phpcsFile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = $e->getLineWithinComment() + $commentStart;
            $this->helper->addMessage($e->getMessage(), $line, Magento_Sniffs_Annotations_Helper::FAILED_PARSE);
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $error = 'Function doc comment is empty';
            $this->helper->addMessage($error, $commentStart, Magento_Sniffs_Annotations_Helper::NO_DOC);
            return;
        }

        // The first line of the comment should just be the /** code.
        $eolPos = strpos($commentString, $phpcsFile->eolChar);
        $firstLine = substr($commentString, 0, $eolPos);
        if ($firstLine !== '/**') {
            $error = 'The open comment tag must be the only content on the line';
            $this->helper->addMessage($error, $commentStart, Magento_Sniffs_Annotations_Helper::CONTENT_AFTER_OPEN);
        }

        // If the comment has an inherit doc note just move on
        if (preg_match('/\{\@inheritdoc\}/', $commentString)) {
            return;
        } elseif (preg_match('/\{?\@?inherit[dD]oc\}?/', $commentString)) {
            $error = 'The incorrect inherit doc tag usage. Should be {@inheritdoc}';
            $this->helper->addMessage($error, $commentStart, Magento_Sniffs_Annotations_Helper::INCORRECT_INHERIT_DOC);
            return;
        }

        $this->processParams($commentStart, $commentEnd);
        $this->processSees($commentStart);
        $this->processReturn($commentStart, $commentEnd);
        $this->processThrows($commentStart);

        // Check for a comment description.
        $short = $comment->getShortComment();
        if (trim($short) === '') {
            $error = 'Missing short description in function doc comment';
            $this->helper->addMessage($error, $commentStart, Magento_Sniffs_Annotations_Helper::MISSING_SHORT);
            return;
        }

        // No extra newline before short description.
        $newlineCount = 0;
        $newlineSpan = strspn($short, $phpcsFile->eolChar);
        if ($short !== '' && $newlineSpan > 0) {
            $error = 'Extra newline(s) found before function comment short description';
            $this->helper->addMessage(
                $error,
                $commentStart + 1,
                Magento_Sniffs_Annotations_Helper::SPACING_BEFORE_SHORT
            );
        }

        $newlineCount = substr_count($short, $phpcsFile->eolChar) + 1;

        // Exactly one blank line between short and long description.
        $long = $comment->getLongComment();
        if (empty($long) === false) {
            $between = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsFile->eolChar);
            if ($newlineBetween !== 2) {
                $error = 'There must be exactly one blank line between descriptions in function comment';
                $this->helper->addMessage(
                    $error,
                    $commentStart + $newlineCount + 1,
                    Magento_Sniffs_Annotations_Helper::SPACING_BETWEEN
                );
            }
            $newlineCount += $newlineBetween;
            $testLong = trim($long);
            if (preg_match('|\p{Lu}|u', $testLong[0]) === 0) {
                $error = 'Function comment long description must start with a capital letter';
                $this->helper->addMessage(
                    $error,
                    $commentStart + $newlineCount,
                    Magento_Sniffs_Annotations_Helper::LONG_NOT_CAPITAL
                );
            }
        }

        // Exactly one blank line before tags.
        $params = $this->commentParser->getTagOrders();
        if (count($params) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = 'There must be exactly one blank line before the tags in function comment';
                if ($long !== '') {
                    $newlineCount += substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1;
                }

                $this->helper->addMessage(
                    $error,
                    $commentStart + $newlineCount,
                    Magento_Sniffs_Annotations_Helper::SPACING_BEFORE_TAGS
                );
                $short = rtrim($short, $phpcsFile->eolChar . ' ');
            }
        }

        // Short description must be single line and end with a full stop.
        $testShort = trim($short);
        $lastChar = $testShort[strlen($testShort) - 1];
        if (substr_count($testShort, $phpcsFile->eolChar) !== 0) {
            $error = 'Function comment short description must be on a single line';
            $this->helper->addMessage($error, $commentStart + 1, Magento_Sniffs_Annotations_Helper::SHORT_SINGLE_LINE);
        }

        if (preg_match('|\p{Lu}|u', $testShort[0]) === 0) {
            $error = 'Function comment short description must start with a capital letter';
            $this->helper->addMessage($error, $commentStart + 1, Magento_Sniffs_Annotations_Helper::SHORT_NOT_CAPITAL);
        }

        if ($lastChar !== '.') {
            $error = 'Function comment short description must end with a full stop';
            $this->helper->addMessage($error, $commentStart + 1, Magento_Sniffs_Annotations_Helper::SHORT_FULL_STOP);
        }

        // Check for unknown/deprecated tags.
        // For example call: $this->processUnknownTags($commentStart, $commentEnd);

        // The last content should be a newline and the content before
        // that should not be blank. If there is more blank space
        // then they have additional blank lines at the end of the comment.
        $words = $this->commentParser->getWords();
        $lastPos = count($words) - 1;
        if (trim(
            $words[$lastPos - 1]
        ) !== '' || strpos(
            $words[$lastPos - 1],
            $this->helper->getCurrentFile()->eolChar
        ) === false || trim(
            $words[$lastPos - 2]
        ) === ''
        ) {
            $error = 'Additional blank lines found at end of function comment';
            $this->helper->addMessage($error, $commentEnd, Magento_Sniffs_Annotations_Helper::SPACING_AFTER);
        }
    }

    /**
     * Process the see tags.
     *
     * @param int $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processSees($commentStart)
    {
        $sees = $this->commentParser->getSees();
        if (empty($sees) === false) {
            $tagOrder = $this->commentParser->getTagOrders();
            $index = array_keys($this->commentParser->getTagOrders(), 'see');
            foreach ($sees as $i => $see) {
                $errorPos = $commentStart + $see->getLine();
                $since = array_keys($tagOrder, 'since');
                if (count($since) === 1 && $this->_tagIndex !== 0) {
                    $this->_tagIndex++;
                    if ($index[$i] !== $this->_tagIndex) {
                        $error = 'The @see tag is in the wrong order; the tag precedes @return';
                        $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::SEE_ORDER);
                    }
                }

                $content = $see->getContent();
                if (empty($content) === true) {
                    $error = 'Content missing for @see tag in function comment';
                    $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::EMPTY_SEE);
                    continue;
                }
            }
        }
    }

    /**
     * Process the return comment of this function comment.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processReturn($commentStart, $commentEnd)
    {
        // Skip constructor and destructor.
        $className = '';
        if ($this->_classToken !== null) {
            $className = $this->helper->getCurrentFile()->getDeclarationName($this->_classToken);
            return $this->helper->getCurrentFile();
            $className = strtolower(ltrim($className, '_'));
        }

        $methodName = strtolower(ltrim($this->_methodName, '_'));
        $isSpecialMethod = $this->_methodName === '__construct' || $this->_methodName === '__destruct';
        $return = $this->commentParser->getReturn();

        if ($isSpecialMethod === false && $methodName !== $className) {
            if ($return !== null) {
                $tagOrder = $this->commentParser->getTagOrders();
                $index = array_keys($tagOrder, 'return');
                $errorPos = $commentStart + $return->getLine();
                $content = trim($return->getRawContent());

                if (count($index) > 1) {
                    $error = 'Only 1 @return tag is allowed in function comment';
                    $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::DUPLICATE_RETURN);
                    return;
                }

                $since = array_keys($tagOrder, 'since');
                if (count($since) === 1 && $this->_tagIndex !== 0) {
                    $this->_tagIndex++;
                    if ($index[0] !== $this->_tagIndex) {
                        $error = 'The @return tag is in the wrong order; the tag follows @see (if used)';
                        $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::RETURN_ORDER);
                    }
                }

                if (empty($content) === true) {
                    $error = 'Return type missing for @return tag in function comment';
                    $this->helper->addMessage(
                        $error,
                        $errorPos,
                        Magento_Sniffs_Annotations_Helper::MISSING_RETURN_TYPE
                    );
                } else {
                    // Check return type (can be multiple, separated by '|').
                    $typeNames = explode('|', $content);
                    $suggestedNames = array();
                    foreach ($typeNames as $i => $typeName) {
                        $suggestedName = $this->helper->suggestType($typeName);
                        if (in_array($suggestedName, $suggestedNames) === false) {
                            $suggestedNames[] = $suggestedName;
                        }
                    }

                    $suggestedType = implode('|', $suggestedNames);
                    if ($content !== $suggestedType) {
                        $error = 'Function return type "%s" is invalid';
                        $data = array($content);
                        $this->helper->addMessage(
                            $error,
                            $errorPos,
                            Magento_Sniffs_Annotations_Helper::INVALID_RETURN,
                            $data
                        );
                    } elseif ($content === 'array' || $content === 'mixed') {
                        // Warn about ambiguous types ie array or mixed
                        $error = 'Ambiguous type "%s" for @return is NOT recommended';
                        $data = array($typeName);
                        $this->helper->addMessage(
                            $error,
                            $errorPos,
                            Magento_Sniffs_Annotations_Helper::AMBIGUOUS_TYPE,
                            $data
                        );
                    }

                    $tokens = $this->helper->getCurrentFile()->getTokens();

                    // If the return type is void, make sure there is
                    // no return statement in the function.
                    if ($content === 'void') {
                        if (isset($tokens[$this->_functionToken]['scope_closer']) === true) {
                            $endToken = $tokens[$this->_functionToken]['scope_closer'];

                            $tokens = $this->helper->getCurrentFile()->getTokens();
                            for ($returnToken = $this->_functionToken; $returnToken < $endToken; $returnToken++) {
                                if ($tokens[$returnToken]['code'] === T_CLOSURE) {
                                    $returnToken = $tokens[$returnToken]['scope_closer'];
                                    continue;
                                }

                                if ($tokens[$returnToken]['code'] === T_RETURN) {
                                    break;
                                }
                            }

                            if ($returnToken !== $endToken) {
                                // If the function is not returning anything, just
                                // exiting, then there is no problem.
                                $semicolon = $this->helper->getCurrentFile()->findNext(
                                    T_WHITESPACE,
                                    $returnToken + 1,
                                    null,
                                    true
                                );
                                if ($tokens[$semicolon]['code'] !== T_SEMICOLON) {
                                    $error = 'Function return type is void, but function contains return statement';
                                    $this->helper->addMessage(
                                        $error,
                                        $errorPos,
                                        Magento_Sniffs_Annotations_Helper::INVALID_RETURN_VOID
                                    );
                                }
                            }
                        }
                    } elseif ($content !== 'mixed') {
                        // If return type is not void, there needs to be a
                        // returns statement somewhere in the function that
                        // returns something.
                        if (isset($tokens[$this->_functionToken]['scope_closer']) === true) {
                            $endToken = $tokens[$this->_functionToken]['scope_closer'];
                            $returnToken = $this->helper->getCurrentFile()->findNext(
                                T_RETURN,
                                $this->_functionToken,
                                $endToken
                            );
                            if ($returnToken === false) {
                                $error = 'Function return type is not void, but function has no return statement';
                                $this->helper->addMessage(
                                    $error,
                                    $errorPos,
                                    Magento_Sniffs_Annotations_Helper::INVALID_NO_RETURN
                                );
                            } else {
                                $semicolon = $this->helper->getCurrentFile()->findNext(
                                    T_WHITESPACE,
                                    $returnToken + 1,
                                    null,
                                    true
                                );
                                if ($tokens[$semicolon]['code'] === T_SEMICOLON) {
                                    $error = 'Function return type is not void, but function is returning void here';
                                    $this->helper->addMessage(
                                        $error,
                                        $returnToken,
                                        Magento_Sniffs_Annotations_Helper::INVALID_RETURN_NOT_VOID
                                    );
                                }
                            }
                        }
                    }

                    $spacing = substr_count($return->getWhitespaceBeforeValue(), ' ');
                    if ($spacing !== 1) {
                        $error = '@return tag indented incorrectly; expected 1 space but found %s';
                        $data = array($spacing);
                        $this->helper->addMessage(
                            $error,
                            $errorPos,
                            Magento_Sniffs_Annotations_Helper::RETURN_INDENT,
                            $data
                        );
                    }
                }
            } else {
                $error = 'Missing @return tag in function comment';
                $this->helper->addMessage($error, $commentEnd, Magento_Sniffs_Annotations_Helper::MISSING_RETURN);
            }
        } elseif ($return !== null) {
            // No return tag for constructor and destructor.
            $errorPos = $commentStart + $return->getLine();
            $error = '@return tag is not required for constructor and destructor';
            $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::RETURN_NOT_REQUIRED);
        }
    }

    /**
     * Process any throw tags that this function comment has.
     *
     * @param int $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processThrows($commentStart)
    {
        if (count($this->commentParser->getThrows()) === 0) {
            return;
        }

        $tagOrder = $this->commentParser->getTagOrders();
        $index = array_keys($this->commentParser->getTagOrders(), 'throws');

        foreach ($this->commentParser->getThrows() as $i => $throw) {
            $exception = $throw->getValue();
            $content = trim($throw->getComment());
            $errorPos = $commentStart + $throw->getLine();
            if (empty($exception) === true) {
                $error = 'Exception type and comment missing for @throws tag in function comment';
                $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::INVALID_THROWS);
            } elseif (empty($content) === true) {
                $error = 'Comment missing for @throws tag in function comment';
                $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::EMPTY_THROWS);
            } else {
                // Assumes that $content is not empty.
                // Starts with a capital letter and ends with a fullstop.
                $firstChar = $content[0];
                if (strtoupper($firstChar) !== $firstChar) {
                    $error = '@throws tag comment must start with a capital letter';
                    $this->helper->addMessage(
                        $error,
                        $errorPos,
                        Magento_Sniffs_Annotations_Helper::THROWS_NOT_CAPITAL
                    );
                }

                $lastChar = $content[strlen($content) - 1];
                if ($lastChar !== '.') {
                    $error = '@throws tag comment must end with a full stop';
                    $this->helper->addMessage(
                        $error,
                        $errorPos,
                        Magento_Sniffs_Annotations_Helper::THROWS_NO_FULL_STOP
                    );
                }
            }

            $since = array_keys($tagOrder, 'since');
            if (count($since) === 1 && $this->_tagIndex !== 0) {
                $this->_tagIndex++;
                if ($index[$i] !== $this->_tagIndex) {
                    $error = 'The @throws tag is in the wrong order; the tag follows @return';
                    $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::THROWS_ORDER);
                }
            }
        }
    }

    /**
     * Process the function parameter comments.
     *
     * @param int $commentStart The position in the stack where
     *                          the comment started.
     * @param int $commentEnd   The position in the stack where
     *                          the comment ended.
     *
     * @return void
     */
    protected function processParams($commentStart, $commentEnd)
    {
        $realParams = $this->helper->getCurrentFile()->getMethodParameters($this->_functionToken);
        $params = $this->commentParser->getParams();
        $foundParams = array();

        if (empty($params) === false) {

            if (substr_count(
                $params[count($params) - 1]->getWhitespaceAfter(),
                $this->helper->getCurrentFile()->eolChar
            ) !== 2
            ) {
                $error = 'Last parameter comment requires a blank newline after it';
                $errorPos = $params[count($params) - 1]->getLine() + $commentStart;
                $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::SPACING_AFTER_PARAMS);
            }

            // Parameters must appear immediately after the comment.
            if ($params[0]->getOrder() !== 2) {
                $error = 'Parameters must appear immediately after the comment';
                $errorPos = $params[0]->getLine() + $commentStart;
                $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::SPACING_BEFORE_PARAMS);
            }

            $previousParam = null;
            $spaceBeforeVar = 10000;
            $spaceBeforeComment = 10000;
            $longestType = 0;
            $longestVar = 0;

            foreach ($params as $param) {

                $paramComment = trim($param->getComment());
                $errorPos = $param->getLine() + $commentStart;

                // Make sure that there is only one space before the var type.
                if ($param->getWhitespaceBeforeType() !== ' ') {
                    $error = 'Expected 1 space before variable type';
                    $this->helper->addMessage(
                        $error,
                        $errorPos,
                        Magento_Sniffs_Annotations_Helper::SPACING_BEFORE_PARAM_TYPE
                    );
                }

                $spaceCount = substr_count($param->getWhitespaceBeforeVarName(), ' ');
                if ($spaceCount < $spaceBeforeVar) {
                    $spaceBeforeVar = $spaceCount;
                    $longestType = $errorPos;
                }

                $spaceCount = substr_count($param->getWhitespaceBeforeComment(), ' ');

                if ($spaceCount < $spaceBeforeComment && $paramComment !== '') {
                    $spaceBeforeComment = $spaceCount;
                    $longestVar = $errorPos;
                }

                // Make sure they are in the correct order, and have the correct name.
                $pos = $param->getPosition();
                $paramName = $param->getVarName() !== '' ? $param->getVarName() : '[ UNKNOWN ]';

                if ($previousParam !== null) {
                    $previousName = $previousParam->getVarName() !== '' ? $previousParam->getVarName() : 'UNKNOWN';
                }

                // Variable must be one of the supported standard type.
                $typeNames = explode('|', $param->getType());
                foreach ($typeNames as $typeName) {
                    $suggestedName = $this->helper->suggestType($typeName);
                    if ($typeName !== $suggestedName) {
                        $error = 'Expected "%s"; found "%s" for %s at position %s';
                        $data = array($suggestedName, $typeName, $paramName, $pos);
                        $this->helper->addMessage(
                            $error,
                            $errorPos,
                            Magento_Sniffs_Annotations_Helper::INCORRECT_PARAM_VAR_NAME,
                            $data
                        );
                    } elseif ($typeName === 'array' || $typeName === 'mixed') {
                        // Warn about ambiguous types ie array or mixed
                        $error = 'Ambiguous type "%s" for %s at position %s is NOT recommended';
                        $data = array($typeName, $paramName, $pos);
                        $this->helper->addMessage(
                            $error,
                            $commentEnd + 2,
                            Magento_Sniffs_Annotations_Helper::AMBIGUOUS_TYPE,
                            $data
                        );
                    } elseif (count($typeNames) === 1) {
                        // Check type hint for array and custom type.
                        $suggestedTypeHint = '';
                        if (strpos($suggestedName, 'array') !== false) {
                            $suggestedTypeHint = 'array';
                        } elseif (strpos($suggestedName, 'callable') !== false) {
                            $suggestedTypeHint = 'callable';
                        } elseif (in_array($typeName, $this->helper->getAllowedTypes()) === false) {
                            $suggestedTypeHint = $suggestedName;
                        } else {
                            $suggestedTypeHint = $this->helper->suggestType($typeName);
                        }

                        if ($suggestedTypeHint !== '' && isset($realParams[$pos - 1]) === true) {
                            $typeHint = $realParams[$pos - 1]['type_hint'];
                            if ($typeHint === '') {
                                $error = 'Type hint "%s" missing for %s at position %s';
                                $data = array($suggestedTypeHint, $paramName, $pos);
                                $this->helper->addMessage(
                                    $error,
                                    $commentEnd + 2,
                                    Magento_Sniffs_Annotations_Helper::TYPE_HINT_MISSING,
                                    $data
                                );
                            } elseif ($typeHint !== $suggestedTypeHint) {
                                $error = 'Expected type hint "%s"; found "%s" for %s at position %s';
                                $data = array($suggestedTypeHint, $typeHint, $paramName, $pos);
                                $this->helper->addMessage(
                                    $error,
                                    $commentEnd + 2,
                                    Magento_Sniffs_Annotations_Helper::INCORRECT_TYPE_HINT,
                                    $data
                                );
                            }
                        } elseif ($suggestedTypeHint === '' && isset($realParams[$pos - 1]) === true) {
                            $typeHint = $realParams[$pos - 1]['type_hint'];
                            if ($typeHint !== '') {
                                $error = 'Unknown type hint "%s" found for %s at position %s';
                                $data = array($typeHint, $paramName, $pos);
                                $this->helper->addMessage(
                                    $error,
                                    $commentEnd + 2,
                                    Magento_Sniffs_Annotations_Helper::INVALID_TYPE_HINT,
                                    $data
                                );
                            }
                        }
                    }
                }

                // Make sure the names of the parameter comment matches the
                // actual parameter.
                if (isset($realParams[$pos - 1]) === true) {
                    $realName = $realParams[$pos - 1]['name'];
                    $foundParams[] = $realName;

                    // Append ampersand to name if passing by reference.
                    if ($realParams[$pos - 1]['pass_by_reference'] === true) {
                        $realName = '&' . $realName;
                    }

                    if ($realName !== $paramName) {
                        $code = Magento_Sniffs_Annotations_Helper::PARAM_NAME_NO_MATCH;
                        $data = array($paramName, $realName, $pos);

                        $error = 'Doc comment for var %s does not match ';
                        if (strtolower($paramName) === strtolower($realName)) {
                            $error .= 'case of ';
                            $code = Magento_Sniffs_Annotations_Helper::PARAM_NAME_NO_CASE_MATCH;
                        }

                        $error .= 'actual variable name %s at position %s';

                        $this->helper->addMessage($error, $errorPos, $code, $data);
                    }
                } elseif (substr($paramName, -4) !== ',...') {
                    // We must have an extra parameter comment.
                    $error = 'Superfluous doc comment at position ' . $pos;
                    $this->helper->addMessage(
                        $error,
                        $errorPos,
                        Magento_Sniffs_Annotations_Helper::EXTRA_PARAM_COMMENT
                    );
                }

                if ($param->getVarName() === '') {
                    $error = 'Missing parameter name at position ' . $pos;
                    $this->helper->addMessage(
                        $error,
                        $errorPos,
                        Magento_Sniffs_Annotations_Helper::MISSING_PARAM_NAME
                    );
                }

                if ($param->getType() === '') {
                    $error = 'Missing type at position ' . $pos;
                    $this->helper->addMessage(
                        $error,
                        $errorPos,
                        Magento_Sniffs_Annotations_Helper::MISSING_PARAM_TYPE
                    );
                }

                if ($paramComment === '') {
                    $error = 'Missing comment for param "%s" at position %s';
                    $data = array($paramName, $pos);
                    $this->helper->addMessage(
                        $error,
                        $errorPos,
                        Magento_Sniffs_Annotations_Helper::MISSING_PARAM_COMMENT,
                        $data
                    );
                } else {
                    // Param comments must start with a capital letter and
                    // end with the full stop.
                    $firstChar = $paramComment[0];
                    if (preg_match('|\p{Lu}|u', $firstChar) === 0) {
                        $error = 'Param comment must start with a capital letter';
                        $this->helper->addMessage(
                            $error,
                            $errorPos,
                            Magento_Sniffs_Annotations_Helper::PARAM_COMMENT_NOT_CAPITAL
                        );
                    }
                    $lastChar = $paramComment[strlen($paramComment) - 1];
                    if ($lastChar !== '.') {
                        $error = 'Param comment must end with a full stop';
                        $this->helper->addMessage(
                            $error,
                            $errorPos,
                            Magento_Sniffs_Annotations_Helper::PARAM_COMMENT_FULL_STOP
                        );
                    }
                }

                $previousParam = $param;
            }

            if ($spaceBeforeVar !== 1 && $spaceBeforeVar !== 10000 && $spaceBeforeComment !== 10000) {
                $error = 'Expected 1 space after the longest type';
                $this->helper->addMessage(
                    $error,
                    $longestType,
                    Magento_Sniffs_Annotations_Helper::SPACING_AFTER_LONG_TYPE
                );
            }

            if ($spaceBeforeComment !== 1 && $spaceBeforeComment !== 10000) {
                $error = 'Expected 1 space after the longest variable name';
                $this->helper->addMessage(
                    $error,
                    $longestVar,
                    Magento_Sniffs_Annotations_Helper::SPACING_AFTER_LONG_NAME
                );
            }
        }

        $realNames = array();
        foreach ($realParams as $realParam) {
            $realNames[] = $realParam['name'];
        }

        // Report missing comments.
        $diff = array_diff($realNames, $foundParams);
        foreach ($diff as $neededParam) {
            if (count($params) !== 0) {
                $errorPos = $params[count($params) - 1]->getLine() + $commentStart;
            } else {
                $errorPos = $commentStart;
            }

            $error = 'Doc comment for "%s" missing';
            $data = array($neededParam);
            $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::MISSING_PARAM_TAG, $data);
        }
    }
}
