<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profiles run block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Run extends Mage_Adminhtml_Block_Abstract
{
    public function getProfile()
    {
        return Mage::registry('current_convert_profile');
    }

    protected function _toHtml()
    {
        $profile = $this->getProfile();

        echo '<html><head>
<script type="text/javascript" src="http://magento-victor.kiev-dev/js/proxy.php?c=auto&f=,prototype/prototype.js,scriptaculous/builder.js,scriptaculous/effects.js,scriptaculous/dragdrop.js,scriptaculous/controls.js,scriptaculous/slider.js,prototype/validation.js,varien/js.js,mage/translate.js,mage/adminhtml/hash.js,mage/adminhtml/events.js,mage/adminhtml/loader.js,mage/adminhtml/grid.js,mage/adminhtml/tabs.js,mage/adminhtml/form.js,mage/adminhtml/accordion.js" ></script>
    <style type="text/css">
    ul { list-style-type:none; padding:0; margin:0; }
    li { margin-left:0; border:solid #CCC 1px; margin:2px; padding:2px 2px 2px 2px; font:normal 12px sans-serif; }
    img { margin-right:5px; }
    </style>
    <title>'.($profile->getId() ? $this->htmlEscape($profile->getName()) : $this->__('No profile')).'</title>
</head><body>';
        echo '<ul>';
        echo '<li>';
        if ($profile->getId()) {
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/note_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("Starting profile execution, please wait...");
            echo '</li>';
            echo '<li style="background-color:#FFD;">';
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/fam_bullet_error.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("Warning: Please don't close window during importing/exporting data");
            echo '</li>';
        } else {
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("No profile loaded...");
        }
        echo '</li>';
        echo '</ul>';

        if ($profile->getId()) {

            echo '<ul>';

            ob_implicit_flush();
            $profile->run();
            foreach ($profile->getExceptions() as $e) {
                switch ($e->getLevel()) {
                    case Varien_Convert_Exception::FATAL:
                        $img = 'error_msg_icon.gif';
                        $liStyle = 'background-color:#FBB; ';
                        break;
                    case Varien_Convert_Exception::ERROR:
                        $img = 'error_msg_icon.gif';
                        $liStyle = 'background-color:#FDD; ';
                        break;
                    case Varien_Convert_Exception::WARNING:
                        $img = 'fam_bullet_error.gif';
                        $liStyle = 'background-color:#FFD; ';
                        break;
                    case Varien_Convert_Exception::NOTICE:
                        $img = 'fam_bullet_success.gif';
                        $liStyle = 'background-color:#DDF; ';
                        break;
                }
                echo '<li style="'.$liStyle.'">';
                echo '<img src="'.Mage::getDesign()->getSkinUrl('images/'.$img).'" class="v-middle"/>';
                echo $e->getMessage();
                if ($e->getPosition()) {
                    echo " <small>(".$e->getPosition().")</small>";
                }
                echo "</li>";
            }

            echo '<li id="liFinished" style="display:none;">';
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/note_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("Finished profile execution.");
            echo '</li>';

            echo "</ul>";


            $showFinished = true;
            $batchModel = Mage::getSingleton('dataflow/batch');
            if ($batchModel->getId()) {
                if ($batchModel->getAdapter()) {
echo '
<script type="text/javascript"></script>
<script type="text/javascript">
function sendImportData(data) {
    new Ajax.Request("'.Mage::getUrl('*/*/batchRun').'", {
      method: "post",
      parameters: data,
      onSuccess: function(transport) {
        alert("123")
      }
    });
}
</script>
';
                    $showFinished = false;
                    $batchImportModel = $batchModel->getBatchImportModel();
                    $importIds = $batchImportModel->getIdCollection();

                    $jsonIds = array_chunk($importIds, 50);
                    foreach ($jsonIds as $part => $ids) {
                        $data = array(
                            'batch_id'   => $batchModel->getId(),
                            'rows[]'     => $ids
                        );
                        echo '<script type="text/javascript">sendImportData('.Zend_Json::encode($data).')</script>';
                    }

                    print $this->getUrl('*/*/batchFinish', array('id' => $batchModel->getId()));
                }
                else {
                    $batchModel->delete();
                }
            }

            if ($showFinished) {
                echo "<script type=\"text/javascript\">$('liFinished').display = '';</script>";
            }
        }
        /*
        echo '<li>';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/note_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
        echo $this->__("Finished profile execution.");
        echo '</li>';
        echo "</ul>";
        */
        echo '</body></html>';
        exit;
    }
}
