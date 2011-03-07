<?php
/**
 * Extended Setup class
 * 
 * @package phpDocumentorCustom
 */
class phpDocumentor_setupCustom extends phpDocumentor_setup
{
    /**
     * Path to custom tags classes
     * @var string
     */
    private $tagsPath = '';

    /**
     * Setup constructor
     * 
     * @see phpDocumentor_setup::phpDocumentor_setup()
     * @param string $tags_path Path to custom tags classes
     */
    function __construct($tags_path = '')
    {
        global $_phpDocumentor_cvsphpfile_exts, $_phpDocumentor_setting;

        $this->tagsPath = $tags_path;

        if (!function_exists('is_a'))
        {
            print "phpDocumentor requires PHP version 4.2.0 or greater to function";
            exit;
        }

        // Added:
        // Load custom tags
        $this->setup = new Io;
        $this->_loadCustomTags();

        if (!isset($interface) && !isset($_GET['interface']) && !isset($_phpDocumentor_setting))
        {
            // Parse the argv settings
            $_phpDocumentor_setting = $this->setup->parseArgv();
        }
        if (isset($_phpDocumentor_setting['useconfig']) &&
             !empty($_phpDocumentor_setting['useconfig'])) {
            $this->readConfigFile($_phpDocumentor_setting['useconfig']);
        }

        // set runtime to a large value since this can take quite a while
        // we can only set_time_limit when not in safe_mode bug #912064
        if (!ini_get('safe_mode'))
        {
            set_time_limit(0);    // unlimited runtime
        } else
        {
            phpDocumentor_out("time_limit cannot be set since your in safe_mode, please edit time_limit in your php.ini to allow enough time for phpDocumentor to run");
        }

        $phpver = phpversion();
        $phpdocver = PHPDOCUMENTOR_VER;
        if (isset($_GET['interface'])) {
            $phpver = "<b>$phpver</b>";
            $phpdocver = "<b>$phpdocver</b>";
        }
        phpDocumentor_out("PHP Version $phpver\n");
        phpDocumentor_out("phpDocumentor version $phpdocver\n\n");

        $this->parseIni();
        $this->setMemoryLimit();

        /*
         * NOTE:
         * It is possible for the tokenizer extension to be loaded,
         * but actually be broken in the OS, and therefore not working...
         * the conditional below will NOT recognize this scenario.
         * You can separately run the {@link tokenizer_test.php} to
         * verify that the tokenizer library is working correctly
         * from the OS perspective.
         */
        if (tokenizer_ext) {
            phpDocumentor_out("using tokenizer Parser\n");
            $this->parse = new phpDocumentorTParser;
        } else {
            phpDocumentor_out("No Tokenizer support detected, so using default (slower) Parser..." . PHP_EOL);

            if (version_compare(phpversion(), '4.3.0', '<')) {
                phpDocumentor_out("    for faster parsing, recompile PHP with --enable-tokenizer." . PHP_EOL );
            } else {
                phpDocumentor_out("    for faster parsing, recompile PHP without --disable-tokenizer." . PHP_EOL );
            }

            $this->parse = new Parser;
        }
    }

    /**
     * Parse configuration file phpDocumentor.ini
     */
    function parseIni()
    {
        phpDocumentor_out("Parsing configuration file phpDocumentor.ini...\n");
        flush();
        if ('@DATA-DIR@' != '@'.'DATA-DIR@')
        {
            $options = phpDocumentor_parse_ini_file(str_replace('\\','/', '@DATA-DIR@/PhpDocumentor') . PATH_DELIMITER . 'phpDocumentor.ini',true);
            phpDocumentor_out("   (found in " . '@DATA-DIR@/PhpDocumentor' . PATH_DELIMITER . ")...\n");
        } else {
            $options = phpDocumentor_parse_ini_file(str_replace('\\','/',$GLOBALS['_phpDocumentor_install_dir']) . PATH_DELIMITER . 'phpDocumentor.ini',true);
            phpDocumentor_out("   (found in " . $GLOBALS['_phpDocumentor_install_dir'] . PATH_DELIMITER . ")...\n");
        }

        if (!$options)
        {
            print "ERROR: cannot open phpDocumentor.ini in directory " . $GLOBALS['_phpDocumentor_install_dir']."\n";
            print "-Is phpdoc in either the path or include_path in your php.ini file?";
            exit;
        }
        
        foreach($options as $var => $values)
        {
            // Deleted:
            // Hardcoded config section filter
            if ($var != 'DEBUG')
            {
                $GLOBALS[$var] = $values;
            }
        }

        phpDocumentor_out("\ndone\n");
        flush();
        /** Debug Constant */
        if (!defined('PHPDOCUMENTOR_DEBUG')) define("PHPDOCUMENTOR_DEBUG",$options['DEBUG']['PHPDOCUMENTOR_DEBUG']);
        if (!defined('PHPDOCUMENTOR_KILL_WHITESPACE')) define("PHPDOCUMENTOR_KILL_WHITESPACE",$options['DEBUG']['PHPDOCUMENTOR_KILL_WHITESPACE']);
        $GLOBALS['_phpDocumentor_cvsphpfile_exts'] = $GLOBALS['_phpDocumentor_phpfile_exts'];
        foreach($GLOBALS['_phpDocumentor_cvsphpfile_exts'] as $key => $val)
        {
            $GLOBALS['_phpDocumentor_cvsphpfile_exts'][$key] = "$val,v";
        }
        // none of this stuff is used anymore
        if (isset($GLOBALS['_phpDocumentor_html_allowed']))
        {
            $___htmltemp = array_flip($GLOBALS['_phpDocumentor_html_allowed']);
            $___html1 = array();
            foreach($___htmltemp as $tag => $trans)
            {
                $___html1['<'.$tag.'>'] = htmlentities('<'.$tag.'>');
                $___html1['</'.$tag.'>'] = htmlentities('</'.$tag.'>');
            }
            $GLOBALS['phpDocumentor___html'] = array_flip($___html1);
        }
    }

    /**
     * Custom tags loader
     */
    private function _loadCustomTags()
    {
        if (empty($this->tagsPath)) return;

        // Load all custom tags files
        foreach (glob($this->tagsPath . '/*.php') as $tag_file) {
            require_once($tag_file);

            $tag = basename($tag_file, '.php');
            
            if (class_exists($tag)) {
                // Get phpDocOptions array
                $tag_vars = get_class_vars($tag);


                // Merge phpDocOptions with command line parameters array
                if (is_array($tag_vars['phpDocOptions'])) {
                    $this->setup->phpDocOptions = array_merge(
                                array_slice($this->setup->phpDocOptions, 0, count($this->setup->phpDocOptions) - 1),
                                $tag_vars['phpDocOptions'],
                                array_slice($this->setup->phpDocOptions, -1, 1)
                            );
                }
                
            }
        }
    }

}