<?php

class Mage_Adminhtml_Block_System_Convert_Profile_Run extends Mage_Core_Block_Abstract
{
    public function toHtml()
    {
        echo "<p>".__("Please wait while running import/export profile...")."</p><hr/>";
        ob_flush();

        $profile = Mage::registry('current_convert_profile');
        $profile->run();

        echo '<ul style="list-style-type:none;">';
        foreach ($profile->getExceptions() as $e) {
            $liStyle = "border:solid #CCC 1px; margin:2px; padding:2px 2px 2px 2px; ";
            switch ($e->getLevel()) {
                case Varien_Convert_Exception::FATAL:
                    $img = 'error_msg_icon.gif';
                    $liStyle .= 'background-color:#FBB; ';
                    break;
                case Varien_Convert_Exception::ERROR:
                    $img = 'error_msg_icon.gif';
                    $liStyle .= 'background-color:#FDD; ';
                    break;
                case Varien_Convert_Exception::WARNING:
                    $img = 'fam_bullet_error.gif';
                    $liStyle .= 'background-color:#FFD; ';
                    break;
                case Varien_Convert_Exception::NOTICE:
                    $img = 'fam_bullet_success.gif';
                    $liStyle .= 'background-color:DDF; ';
                    break;
            }
            echo '<li style="'.$liStyle.'">';
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/'.$img).'" align="absmiddle" style="margin-right:5px"/>';
            echo $e->getMessage();
            if ($e->getPosition()) {
                echo " <small>(".$e->getPosition().")</small>";
            }
            echo "</li>";
        }
        echo "</ul>";
    }
}