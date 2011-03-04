<?php

class phpDocumentor_setup_custom extends phpDocumentor_setup
{

    private $tagsPath = '';

    function __construct($tags_path = '')
    {
        global $_phpDocumentor_cvsphpfile_exts, $_phpDocumentor_setting;

        $this->tagsPath = $tags_path;

        if (!function_exists('is_a'))
        {
            print "phpDocumentor requires PHP version 4.2.0 or greater to function";
            exit;
        }

        $this->setup = new Io;
        $this->loadCustomTags();

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

    function loadCustomTags()
    {
        if (empty($this->tagsPath)) return;

        foreach (glob($this->tagsPath . '/*.php') as $tag_file) {
            require_once($tag_file);

            $tag = basename($tag_file, '.php');
            
            if (class_exists($tag)) {
                $tag_class = new $tag(null, new parserStringWithInlineTags());

                if (is_array($tag_class->phpDocOptions)) {
                    $this->setup->phpDocOptions = array_merge(
                                array_slice($this->setup->phpDocOptions, 0 , count($this->setup->phpDocOptions) - 1),
                                $tag_class->phpDocOptions,
                                array_slice($this->setup->phpDocOptions, count($this->setup->phpDocOptions) - 1, 1)
                            );
                }
                
            }
        }
    }

}