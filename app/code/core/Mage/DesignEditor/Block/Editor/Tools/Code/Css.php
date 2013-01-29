<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block that renders CSS tab
 */
class Mage_DesignEditor_Block_Editor_Tools_Code_Css extends Mage_Core_Block_Template
{
    /**
     * Get file groups content
     *
     * @return array
     */
    public function getFileGroups()
    {
        $groups = array();
        foreach ($this->getCssFiles() as $groupName => $files) {
            $groups[] =  $this->getChildBlock('design_editor_tools_code_css_group')
                ->setTitle($groupName)
                ->setFiles($files)
                ->setThemeId($this->getThemeId())
                ->toHtml();
        }

        return $groups;
    }

    /**
     * Get VDE messages content
     *
     * return array
     */
    public function getMessages()
    {
        $items = array(
            array(
                'text' => 'Oops! Your upload did not finish. Try checking that thing that you need to check and try again.'
            ),
            array(
                'text' => 'Oops! Your upload did not finish. Try checking that thing that you need to check and try again.'
            ),
        );

        $messages = array();
        if ($items) {
            foreach ($items as $message) {
                $messages[] = $this->getChildBlock('design_editor_tools_code_css_message')
                    ->setMessageText($message['text'])
                    ->toHtml();
            }
        }

        return $messages;
    }
}
