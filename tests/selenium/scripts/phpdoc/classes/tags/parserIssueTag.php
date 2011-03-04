<?php

class parserIssueTag extends customParserTag
{
    var $keyword = 'issue';

    public $phpDocOptions = array(
        'issue-baseurl' => array(
            'tag'  => array('--issue-baseurl'),
            'desc' => 'base url for links generated from @issue tag',
            'type' => 'value',
        ),
    );

    function parserIssueTag($keyword, $value)
    {
        $id = $value->getString();
        $this->value = new parserStringWithInlineTags();
        $this->value->add($id);

        $base_url = rtrim($this->getSettings('issue','issue-baseurl'), '/');

        if ($base_url) {
            if (false === strpos($base_url, 'http://')) {
                 $base_url = 'http://' . $base_url;
            }

            $link = new parserLinkInlineTag($base_url . '/' . $id, $id);
            $string = new parserStringWithInlineTags;
            $string->add($link);
            $this->value = $string;
        }
    }
}

?>
