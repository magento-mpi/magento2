<?php
/**
 * Squiz_Sniffs_WhiteSpace_SuperfluousWhitespaceSniff.
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
 * Squiz_Sniffs_WhiteSpace_SuperfluousWhitespaceSniff.
 *
 * Checks that no whitespace proceeds the first content of the file, exists
 * after the last content of the file, resides after content on any line, or
 * are two empty lines in functions.
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
class Squiz_Sniffs_WhiteSpace_SuperfluousWhitespaceSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                   'CSS',
                                  );

    /**
     * If TRUE, whitespace rules are not checked for blank lines.
     *
     * Blank lines are those that contain only whitespace.
     *
     * @var boolean
     */
    public $ignoreBlankLines = false;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_OPEN_TAG,
                T_CLOSE_TAG,
                T_WHITESPACE,
                T_COMMENT,
               );

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
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

        if ($tokens[$stackPtr]['code'] === T_OPEN_TAG) {

            /*
                Check for start of file whitespace.
            */

            if ($phpcsFile->tokenizerType !== 'PHP') {
                // The first token is always the open tag inserted when tokenizsed
                // and the second token is always the first piece of content in
                // the file. If the second token is whitespace, there was
                // whitespace at the start of the file.
                if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
                    return;
                }
            } else {
                // If its the first token, then there is no space.
                if ($stackPtr === 0) {
                    return;
                }

                for ($i = ($stackPtr - 1); $i >= 0; $i--) {
                    // If we find something that isn't inline html then there is something previous in the file.
                    if ($tokens[$i]['type'] !== 'T_INLINE_HTML') {
                        return;
                    }

                    // If we have ended up with inline html make sure it isn't just whitespace.
                    $tokenContent = trim($tokens[$i]['content']);
                    if ($tokenContent !== '') {
                        return;
                    }
                }
            }//end if

            $phpcsFile->addError('Additional whitespace found at start of file', $stackPtr, 'StartFile');

        } else if ($tokens[$stackPtr]['code'] === T_CLOSE_TAG) {

            /*
                Check for end of file whitespace.
            */

            if ($phpcsFile->tokenizerType === 'JS') {
                // The last token is always the close tag inserted when tokenized
                // and the second last token is always the last piece of content in
                // the file. If the second last token is whitespace, there was
                // whitespace at the end of the file.
                $stackPtr--;
            } else if ($phpcsFile->tokenizerType === 'CSS') {
                // The last two tokens are always the close tag and whitespace
                // inserted when tokenizsed and the third last token is always the
                // last piece of content in the file. If the third last token is
                // whitespace, there was whitespace at the end of the file.
                $stackPtr -= 2;
            }

            if ($phpcsFile->tokenizerType === 'PHP') {
                if (isset($tokens[($stackPtr + 1)]) === false) {
                    // The close PHP token is the last in the file.
                    return;
                }

                for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
                    // If we find something that isn't inline html then there
                    // is more to the file.
                    if ($tokens[$i]['type'] !== 'T_INLINE_HTML') {
                        return;
                    }

                    // If we have ended up with inline html make sure it
                    // isn't just whitespace.
                    $tokenContent = trim($tokens[$i]['content']);
                    if (empty($tokenContent) === false) {
                        return;
                    }
                }
            } else {
                // The pointer is now looking at the last content in the file and
                // not the fake PHP end tag the tokenizer inserted.
                if ($tokens[$stackPtr]['code'] !== T_WHITESPACE) {
                    return;
                }

                // Allow a single newline at the end of the last line in the file.
                if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE
                    && $tokens[$stackPtr]['content'] === $phpcsFile->eolChar
                ) {
                    return;
                }

            }

            $phpcsFile->addError('Additional whitespace found at end of file', $stackPtr, 'EndFile');

        } else {

            /*
                Check for end of line whitespace.
            */

            // Ignore whitespace that is not at the end of a line.
            if (strpos($tokens[$stackPtr]['content'], $phpcsFile->eolChar) === false) {
                return;
            }

            // Ignore blank lines if required.
            if ($this->ignoreBlankLines === true
                && $tokens[($stackPtr - 1)]['line'] !== $tokens[$stackPtr]['line']
            ) {
                return;
            }

            $tokenContent = rtrim($tokens[$stackPtr]['content'], $phpcsFile->eolChar);
            if (empty($tokenContent) === false) {
                if ($tokenContent !== rtrim($tokenContent)) {
                    $phpcsFile->addError('Whitespace found at end of line', $stackPtr, 'EndLine');
                }
            }

            /*
                Check for multiple blanks lines in a function.
            */

            if ($phpcsFile->hasCondition($stackPtr, T_FUNCTION) === true) {
                if ($tokens[($stackPtr - 1)]['line'] < $tokens[$stackPtr]['line'] && $tokens[($stackPtr - 2)]['line'] === $tokens[($stackPtr - 1)]['line']) {
                    // This is an empty line and the line before this one is not
                    //  empty, so this could be the start of a multiple empty
                    // line block.
                    $next  = $phpcsFile->findNext(T_WHITESPACE, $stackPtr, null, true);
                    $lines = $tokens[$next]['line'] - $tokens[$stackPtr]['line'];
                    if ($lines > 1) {
                        $error = 'Functions must not contain multiple empty lines in a row; found %s empty lines';
                        $data  = array($lines);
                        $phpcsFile->addError($error, $stackPtr, 'EmptyLines', $data);
                    }
                }
            }

        }//end if

    }//end process()


}//end class

?>
