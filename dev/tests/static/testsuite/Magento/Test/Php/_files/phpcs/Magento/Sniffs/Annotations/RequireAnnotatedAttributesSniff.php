<?php
/**
 * Parses and verifies the variable doc comment.
 *
 * Verifies that :
 * <ul>
 *  <li>A variable doc comment exists.</li>
 *  <li>Short description ends with a full stop.</li>
 *  <li>There is a blank line after the short description.</li>
 *  <li>There is a blank line between the description and the tags.</li>
 *  <li>Check the order, indentation and content of each tag.</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 *
 * @SuppressWarnings(PHPMD)
 */
include_once 'Helper.php';
class Magento_Sniffs_Annotations_RequireAnnotatedAttributesSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{
    /**
     * The header comment parser for the current file.
     *
     * @var PHP_CodeSniffer_Comment_Parser_ClassCommentParser
     */
    protected $commentParser = null;

    /**
     * The sniff helper for stuff shared between the annotations sniffs
     *
     * @var Magento_Sniffs_Annotations_Helper
     */
    protected $helper = null;

    /**
     * Called to process class member vars.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->helper = new Magento_Sniffs_Annotations_Helper($phpcsFile);
        $tokens = $phpcsFile->getTokens();
        $commentToken = array(T_COMMENT, T_DOC_COMMENT);

        // Extract the var comment docblock.
        $commentEnd = $phpcsFile->findPrevious($commentToken, $stackPtr - 3);
        if ($commentEnd !== false && $tokens[$commentEnd]['code'] === T_COMMENT) {
            $this->helper->addMessage(
                'You must use "/**" style comments for a variable comment',
                $stackPtr,
                Magento_Sniffs_Annotations_Helper::WRONG_STYLE
            );
            return;
        } elseif ($commentEnd === false || $tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
            $this->helper->addMessage(
                'Missing variable doc comment',
                $stackPtr,
                Magento_Sniffs_Annotations_Helper::MISSING
            );
            return;
        } else {
            // Make sure the comment we have found belongs to us.
            $commentFor = $phpcsFile->findNext(array(T_VARIABLE, T_CLASS, T_INTERFACE), $commentEnd + 1);
            if ($commentFor !== $stackPtr) {
                $this->helper->addMessage(
                    'Missing variable doc comment',
                    $stackPtr,
                    Magento_Sniffs_Annotations_Helper::MISSING
                );
                return;
            }
        }

        $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT, $commentEnd - 1, null, true) + 1;
        $commentString = $phpcsFile->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);

        // Parse the header comment docblock.
        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_MemberCommentParser($commentString, $phpcsFile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = $e->getLineWithinComment() + $commentStart;
            $this->helper->addMessage($e->getMessage(), $line, Magento_Sniffs_Annotations_Helper::ERROR_PARSING);
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $error = 'Variable doc comment is empty';
            $this->helper->addMessage($error, $commentStart, Magento_Sniffs_Annotations_Helper::EMPTY_DOC);
            return;
        }

        // The first line of the comment should just be the /** code.
        $eolPos = strpos($commentString, $phpcsFile->eolChar);
        $firstLine = substr($commentString, 0, $eolPos);
        if ($firstLine !== '/**') {
            $error = 'The open comment tag must be the only content on the line';
            $this->helper->addMessage($error, $commentStart, Magento_Sniffs_Annotations_Helper::CONTENT_AFTER_OPEN);
        }

        // Check for a comment description.
        $short = $comment->getShortComment();
        $long = '';
        if (trim($short) === '') {
            $error = 'Missing short description in variable doc comment';
            $this->helper->addMessage($error, $commentStart, Magento_Sniffs_Annotations_Helper::MISSING_SHORT);
            $newlineCount = 1;
        } else {
            // No extra newline before short description.
            $newlineCount = 0;
            $newlineSpan = strspn($short, $phpcsFile->eolChar);
            if ($short !== '' && $newlineSpan > 0) {
                $error = 'Extra newline(s) found before variable comment short description';
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
                    $error = 'There must be exactly one blank line between descriptions in variable comment';
                    $this->helper->addMessage(
                        $error,
                        $commentStart + $newlineCount + 1,
                        Magento_Sniffs_Annotations_Helper::SPACING_BETWEEN
                    );
                }

                $newlineCount += $newlineBetween;

                $testLong = trim($long);
                if (preg_match('|\p{Lu}|u', $testLong[0]) === 0) {
                    $error = 'Variable comment long description must start with a capital letter';
                    $this->helper->addMessage(
                        $error,
                        $commentStart + $newlineCount,
                        Magento_Sniffs_Annotations_Helper::LONG_NOT_CAPITAL
                    );
                }
            }

            // Short description must be single line and end with a full stop.
            $testShort = trim($short);
            $lastChar = $testShort[strlen($testShort) - 1];
            if (substr_count($testShort, $phpcsFile->eolChar) !== 0) {
                $error = 'Variable comment short description must be on a single line';
                $this->helper->addMessage(
                    $error,
                    $commentStart + 1,
                    Magento_Sniffs_Annotations_Helper::SHORT_SINGLE_LINE
                );
            }

            if (preg_match('|\p{Lu}|u', $testShort[0]) === 0) {
                $error = 'Variable comment short description must start with a capital letter';
                $this->helper->addMessage(
                    $error,
                    $commentStart + 1,
                    Magento_Sniffs_Annotations_Helper::SHORT_NOT_CAPITAL
                );
            }

            if ($lastChar !== '.') {
                $error = 'Variable comment short description must end with a full stop';
                $this->helper->addMessage(
                    $error,
                    $commentStart + 1,
                    Magento_Sniffs_Annotations_Helper::SHORT_FULL_STOP
                );
            }
        }

        // Exactly one blank line before tags.
        $tags = $this->commentParser->getTagOrders();
        if (count($tags) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = 'There must be exactly one blank line before the tags in variable comment';
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

        // Check for unknown/deprecated tags.
        $unknownTags = $this->commentParser->getUnknown();
        foreach ($unknownTags as $errorTag) {
            // Unknown tags are not parsed, do not process further.
            $error = '@%s tag is not allowed in variable comment';
            $data = array($errorTag['tag']);
            $this->helper->addMessage(
                $error,
                $commentStart + $errorTag['line'],
                Magento_Sniffs_Annotations_Helper::TAG_NOT_ALLOWED,
                $data
            );
        }

        // Check each tag.
        $this->processVar($commentStart, $commentEnd);
        $this->processSees($commentStart);

        // The last content should be a newline and the content before
        // that should not be blank. If there is more blank space
        // then they have additional blank lines at the end of the comment.
        $words = $this->commentParser->getWords();
        $lastPos = count($words) - 1;
        if (trim(
            $words[$lastPos - 1]
        ) !== '' || strpos(
            $words[$lastPos - 1],
            $this->currentFile->eolChar
        ) === false || trim(
            $words[$lastPos - 2]
        ) === ''
        ) {
            $error = 'Additional blank lines found at end of variable comment';
            $this->helper->addMessage($error, $commentEnd, Magento_Sniffs_Annotations_Helper::SPACING_AFTER);
        }
    }

    /**
     * Process the var tag.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processVar($commentStart, $commentEnd)
    {
        $var = $this->commentParser->getVar();

        if ($var !== null) {
            $errorPos = $commentStart + $var->getLine();
            $index = array_keys($this->commentParser->getTagOrders(), 'var');

            if (count($index) > 1) {
                $error = 'Only 1 @var tag is allowed in variable comment';
                $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::DUPLICATE_VAR);
                return;
            }

            if ($index[0] !== 1) {
                $error = 'The @var tag must be the first tag in a variable comment';
                $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::VAR_ORDER);
            }

            $content = $var->getContent();
            if (empty($content) === true) {
                $error = 'Var type missing for @var tag in variable comment';
                $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::MISSING_VAR_TYPE);
                return;
            } else {
                $suggestedType = $this->helper->suggestType($content);
                if ($content !== $suggestedType) {
                    $error = 'Expected "%s"; found "%s" for @var tag in variable comment';
                    $data = array($suggestedType, $content);
                    $this->helper->addMessage(
                        $error,
                        $errorPos,
                        Magento_Sniffs_Annotations_Helper::INCORRECT_VAR_TYPE,
                        $data
                    );
                } elseif ($content === 'array' || $content === 'mixed') {
                    // Warn about ambiguous types ie array or mixed
                    $error = 'Ambiguous type "%s" for @var is NOT recommended';
                    $data = array($content);
                    $this->helper->addMessage(
                        $error,
                        $errorPos,
                        Magento_Sniffs_Annotations_Helper::AMBIGUOUS_TYPE,
                        $data
                    );
                }
            }

            $spacing = substr_count($var->getWhitespaceBeforeContent(), ' ');
            if ($spacing !== 1) {
                $error = '@var tag indented incorrectly; expected 1 space but found %s';
                $data = array($spacing);
                $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::VAR_INDENT, $data);
            }
        } else {
            $error = 'Missing @var tag in variable comment';
            $this->helper->addMessage($error, $commentEnd, Magento_Sniffs_Annotations_Helper::MISSING_VAR);
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
            foreach ($sees as $see) {
                $errorPos = $commentStart + $see->getLine();
                $content = $see->getContent();
                if (empty($content) === true) {
                    $error = 'Content missing for @see tag in variable comment';
                    $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::EMPTY_SEE);
                    continue;
                }

                $spacing = substr_count($see->getWhitespaceBeforeContent(), ' ');
                if ($spacing !== 1) {
                    $error = '@see tag indented incorrectly; expected 1 spaces but found %s';
                    $data = array($spacing);
                    $this->helper->addMessage($error, $errorPos, Magento_Sniffs_Annotations_Helper::SEE_INDENT, $data);
                }
            }
        }
    }

    /**
     * Called to process a normal variable.
     *
     * Not required for this sniff.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                  $stackPtr  The position where the double quoted
     *                                        string was found.
     *
     * @return void
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
    }

    /**
     * Called to process variables found in double quoted strings.
     *
     * Not required for this sniff.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                  $stackPtr  The position where the double quoted
     *                                        string was found.
     *
     * @return void
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
    }
}
