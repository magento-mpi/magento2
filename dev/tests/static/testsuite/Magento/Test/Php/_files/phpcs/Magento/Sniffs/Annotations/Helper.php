<?php
/**
 * Base of the annotations sniffs
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
class Magento_Sniffs_Annotations_Helper
{
    const ERROR_PARSING = 'ErrorParsing';

    const AMBIGUOUS_TYPE = 'AmbiguousType';

    const MISSING = 'Missing';

    const WRONG_STYLE = 'WrongStyle';

    const WRONG_END = 'WrongEnd';

    const FAILED_PARSE = 'FailedParse';

    const CONTENT_AFTER_OPEN = 'ContentAfterOpen';

    const MISSING_SHORT = 'MissingShort';

    const EMPTY_DOC = 'Empty';

    const SPACING_BETWEEN = 'SpacingBetween';

    const SPACING_BEFORE_SHORT = 'SpacingBeforeShort';

    const SPACING_BEFORE_TAGS = 'SpacingBeforeTags';

    const SHORT_SINGLE_LINE = 'ShortSingleLine';

    const SHORT_NOT_CAPITAL = 'ShortNotCapital';

    const SHORT_FULL_STOP = 'ShortFullStop';

    const SPACING_AFTER = 'SpacingAfter';

    const SEE_ORDER = 'SeeOrder';

    const EMPTY_SEE = 'EmptySee';

    const SEE_INDENT = 'SeeIndent';

    const DUPLICATE_RETURN = 'DuplicateReturn';

    const MISSING_PARAM_TAG = 'MissingParamTag';

    const SPACING_AFTER_LONG_NAME = 'SpacingAfterLongName';

    const SPACING_AFTER_LONG_TYPE = 'SpacingAfterLongType';

    const MISSING_PARAM_TYPE = 'MissingParamType';

    const MISSING_PARAM_NAME = 'MissingParamName';

    const EXTRA_PARAM_COMMENT = 'ExtraParamComment';

    const PARAM_NAME_NO_MATCH = 'ParamNameNoMatch';

    const PARAM_NAME_NO_CASE_MATCH = 'ParamNameNoCaseMatch';

    const INVALID_TYPE_HINT = 'InvalidTypeHint';

    const INCORRECT_TYPE_HINT = 'IncorrectTypeHint';

    const TYPE_HINT_MISSING = 'TypeHintMissing';

    const INCORRECT_PARAM_VAR_NAME = 'IncorrectParamVarName';

    const RETURN_ORDER = 'ReturnOrder';

    const MISSING_RETURN_TYPE = 'MissingReturnType';

    const INVALID_RETURN = 'InvalidReturn';

    const INVALID_RETURN_VOID = 'InvalidReturnVoid';

    const INVALID_NO_RETURN = 'InvalidNoReturn';

    const INVALID_RETURN_NOT_VOID = 'InvalidReturnNotVoid';

    const INCORRECT_INHERIT_DOC = 'IncorrectInheritDoc';

    const RETURN_INDENT = 'ReturnIndent';

    const MISSING_RETURN = 'MissingReturn';

    const RETURN_NOT_REQUIRED = 'ReturnNotRequired';

    const INVALID_THROWS = 'InvalidThrows';

    const THROWS_NOT_CAPITAL = 'ThrowsNotCapital';

    const THROWS_ORDER = 'ThrowsOrder';

    const EMPTY_THROWS = 'EmptyThrows';

    const THROWS_NO_FULL_STOP = 'ThrowsNoFullStop';

    const SPACING_AFTER_PARAMS = 'SpacingAfterParams';

    const SPACING_BEFORE_PARAMS = 'SpacingBeforeParams';

    const SPACING_BEFORE_PARAM_TYPE = 'SpacingBeforeParamType';

    const LONG_NOT_CAPITAL = 'LongNotCapital';

    const TAG_NOT_ALLOWED = 'TagNotAllowed';

    const DUPLICATE_VAR = 'DuplicateVar';

    const VAR_ORDER = 'VarOrder';

    const MISSING_VAR_TYPE = 'MissingVarType';

    const INCORRECT_VAR_TYPE = 'IncorrectVarType';

    const VAR_INDENT = 'VarIndent';

    const MISSING_VAR = 'MissingVar';

    const MISSING_PARAM_COMMENT = 'MissingParamComment';

    const PARAM_COMMENT_NOT_CAPITAL = 'ParamCommentNotCapital';

    const PARAM_COMMENT_FULL_STOP = 'ParamCommentFullStop';

    // tells phpcs to use the default level
    const ERROR = 0;

    // default level of warnings is 5
    const WARNING = 6;

    const INFO = 2;

    // Lowest possible level.
    const OFF = 1;

    /**
     * Map of Error Type to Error Severity
     *
     * @var array
     */
    protected static $reportingLevel = array(
        self::ERROR_PARSING => self::ERROR,
        self::AMBIGUOUS_TYPE => self::WARNING,
        self::MISSING => self::WARNING,
        self::WRONG_STYLE => self::WARNING,
        self::WRONG_END => self::WARNING,
        self::FAILED_PARSE => self::ERROR,
        self::EMPTY_DOC => self::WARNING,
        self::CONTENT_AFTER_OPEN => self::WARNING,
        self::MISSING_SHORT => self::WARNING,
        self::SPACING_BETWEEN => self::OFF,
        self::SPACING_BEFORE_SHORT => self::WARNING,
        self::SPACING_BEFORE_TAGS => self::INFO,
        self::SHORT_SINGLE_LINE => self::OFF,
        self::SHORT_NOT_CAPITAL => self::WARNING,
        self::SHORT_FULL_STOP => self::OFF,
        self::SPACING_AFTER => self::WARNING,
        self::SEE_ORDER => self::WARNING,
        self::EMPTY_SEE => self::WARNING,
        self::SEE_INDENT => self::WARNING,
        self::DUPLICATE_RETURN => self::WARNING,
        self::MISSING_PARAM_TAG => self::WARNING,
        self::SPACING_AFTER_LONG_NAME => self::OFF,
        self::SPACING_AFTER_LONG_TYPE => self::OFF,
        self::MISSING_PARAM_TYPE => self::WARNING,
        self::MISSING_PARAM_NAME => self::WARNING,
        self::EXTRA_PARAM_COMMENT => self::WARNING,
        self::PARAM_NAME_NO_MATCH => self::WARNING,
        self::PARAM_NAME_NO_CASE_MATCH => self::WARNING,
        self::INVALID_TYPE_HINT => self::WARNING,
        self::INCORRECT_TYPE_HINT => self::WARNING,
        self::TYPE_HINT_MISSING => self::INFO,
        self::INCORRECT_PARAM_VAR_NAME => self::WARNING,
        self::RETURN_ORDER => self::WARNING,
        self::MISSING_RETURN_TYPE => self::WARNING,
        self::INVALID_RETURN => self::WARNING,
        self::INVALID_RETURN_VOID => self::WARNING,
        self::INVALID_NO_RETURN => self::WARNING,
        self::INVALID_RETURN_NOT_VOID => self::WARNING,
        self::INCORRECT_INHERIT_DOC => self::WARNING,
        self::RETURN_INDENT => self::WARNING,
        self::MISSING_RETURN => self::WARNING,
        self::RETURN_NOT_REQUIRED => self::WARNING,
        self::INVALID_THROWS => self::WARNING,
        self::THROWS_NOT_CAPITAL => self::WARNING,
        self::THROWS_ORDER => self::WARNING,
        self::EMPTY_THROWS => self::OFF,
        self::THROWS_NO_FULL_STOP => self::OFF,
        self::SPACING_AFTER_PARAMS => self::OFF,
        self::SPACING_BEFORE_PARAMS => self::WARNING,
        self::SPACING_BEFORE_PARAM_TYPE => self::WARNING,
        self::LONG_NOT_CAPITAL => self::WARNING,
        self::TAG_NOT_ALLOWED => self::WARNING,
        self::DUPLICATE_VAR => self::WARNING,
        self::VAR_ORDER => self::WARNING,
        self::MISSING_VAR_TYPE => self::WARNING,
        self::INCORRECT_VAR_TYPE => self::WARNING,
        self::VAR_INDENT => self::WARNING,
        self::MISSING_VAR => self::WARNING,
        self::MISSING_PARAM_COMMENT => self::OFF,
        self::PARAM_COMMENT_NOT_CAPITAL => self::OFF,
        self::PARAM_COMMENT_FULL_STOP => self::OFF,
    );

    /**
     * List of allowed types
     *
     * @var string[]
     */
    protected static $allowedTypes = array(
        'array',
        'boolean',
        'bool',
        'float',
        'integer',
        'int',
        'object',
        'string',
        'resource',
        'callable',
        'true',
        'false'
    );

    /**
     * The current PHP_CodeSniffer_File object we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    protected $currentFile = null;

    /**
     * Constructor for class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     */
    public function __construct(PHP_CodeSniffer_File $phpcsFile)
    {
        $this->currentFile = $phpcsFile;
    }

    /**
     * Returns the current file object
     *
     * @return PHP_CodeSniffer_File
     */
    public function getCurrentFile()
    {
        return $this->currentFile;
    }

    /**
     * Returns the array of allowed types for magento standard
     *
     * @return array
     */
    public function getAllowedTypes()
    {
        return self::$allowedTypes;
    }

    /**
     * This method will add the message as an error or warning depending on the configuration
     *
     * @param string $message  The error message.
     * @param int    $stackPtr The stack position where the error occurred.
     * @param string $code     A violation code unique to the sniff message.
     * @param array  $data     Replacements for the error message.
     * @param int    $severity The severity level for this error. A value of 0
     */
    public function addMessage($message, $stackPtr, $code, $data = array(), $severity = 0)
    {
        // Does the $code key exist in the report level
        if (array_key_exists($code, self::$reportingLevel)) {
            $level = self::$reportingLevel[$code];
            if ($level === self::WARNING || $level === self::INFO || $level === self::OFF) {
                $s = $level;
                if ($severity !== 0) {
                    $s = $severity;
                }
                $this->currentFile->addWarning($message, $stackPtr, $code, $data, $s);
            } else {
                $this->currentFile->addError($message, $stackPtr, $code, $data, $severity);
            }
        }
    }

    /**
     * Determine if text is a class name
     *
     * @param string $class
     * @return bool
     */
    protected function isClassName($class)
    {
        $return = false;
        if (preg_match('/^\\\\?[A-Z]\\w+(?:\\\\\\w+)*?$/', $class)) {
            $return = true;
        }
    }

    /**
     * Take the type and suggest the correct one.
     *
     * @param string $type
     * @return string
     */
    public function suggestType($type)
    {
        $suggestedName = null;
        // First check to see if this type is a list of types. If so we break it up and check each
        if (preg_match('/^.*?(?:\|.*)+$/', $type)) {
            // Return list of all types in this string.
            $types = explode('|', $type);
            if (is_array($types)) {
                // Loop over all types and call this method on each.
                $suggestions = array();
                foreach ($types as $t) {
                    $suggestions[] = $this->suggestType($t);
                }
                // Now that we have suggestions put them back together.
                $suggestedName = implode('|', $suggestions);
            } else {
                $suggestedName = 'Unknown';
            }
        } elseif ($this->isClassName($type)) {
            // If this looks like a class name.
            $suggestedName = $type;
        } else {
            // Only one type First check if that type is a base one.
            $lowerVarType = strtolower($type);
            switch ($lowerVarType) {
                case 'bool':
                    $suggestedName = 'bool';
                    break;
                case 'boolean':
                    $suggestedName = 'bool';
                    break;
                case 'int':
                    $suggestedName = 'int';
                    break;
                case 'integer':
                    $suggestedName = 'int';
                    break;
            }
            //end switch
            // If no name suggested yet then call the phpcs version of this method.
            if (empty($suggestedName)) {
                $suggestedName = PHP_CodeSniffer::suggestType($type);
            }
        }
        return $suggestedName;
    }
}
