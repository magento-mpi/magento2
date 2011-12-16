<?php

class Mage_Api2_Model_Renderer_Html implements Mage_Api2_Model_Renderer_Interface
{
    /**
     * Convert Array to HTML tables (horizontal)
     *
     * @param array $data
     * @param null $options
     * @return string
     */
    public function render(array $data, $options = null)
    {
        $docType = '<!DOCTYPE html><html></html>';

        $header = array_keys($data);

        $rows = array();
        $cells = array();
        foreach ($header as $cell) {
            $cells[] = sprintf("<th align=left>%s</th>", htmlspecialchars($cell));
        }
        $row = sprintf("<tr>\n%s</tr>", join("\n", $cells));
        $rows[] = $row;

        $cells = array();
        foreach ($data as $cell) {
            $cells[] = sprintf("<td>%s</td>", htmlspecialchars($cell));
        }
        $rows[] = sprintf("<tr>\n%s</tr>", join("\n", $cells));

        $content = sprintf("%s<table>\n%s\n</table>", $docType, join("\n", $rows));

        return $content;
    }

    public function renderErrors($code, $exceptions)
    {
        $domain = 'core';

        $messages = array();
        /** @var Exception $exception */
        foreach ($exceptions as $exception) {
            $message = array(
                'domain'   => $domain,
                'code'     => $exception->getCode(),
                'message'  => $exception->getMessage(),
            );
            $messages[] = $message;
        }

        $content = Zend_Json::encode(array('messages'=>$messages));

        /*$content = preg_replace('/(:{\")/i', ":{\n\"", $content);
        //$content = preg_replace('/(:{)/i', ":\n{", $content);
        $content = preg_replace('/(,")/i', ",\n\"", $content);
        $content = preg_replace('/("})/i', "\"\n}", $content);
        //$content = preg_replace('/(}})/i', "}\n}", $content);*/

        return $content;
    }
}
