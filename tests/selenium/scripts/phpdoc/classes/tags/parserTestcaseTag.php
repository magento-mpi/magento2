<?php
/**
 * Testcase tag
 *
 * @package phpDocumentorCustom
 */
class parserTestcaseTag extends parserTagCustom
{
    /**
     * Tag keyword
     * @var string
     */
    public $keyword = 'testcase';

    /**
     * Command line options array
     * @var array
     */
    public $phpDocOptions = array(
        'testcase-baseurl' => array(
            'tag'  => array('--testcase-baseurl'),
            'desc' => 'base url for links generated from @testcase tag',
            'type' => 'value',
        ),
    );

    /**
     * Tag parser
     * 
     * @param string $keyword
     * @param mixed $value
     */
    function parserTestcaseTag($keyword, $value)
    {
        $this->generateUrl($keyword, $value, 'testcase-baseurl');
    }
}
