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

        echo '<html><head>';

        $headBlock = $this->getLayout()->createBlock('page/html_head');
        $headBlock->addJs('prototype/prototype.js');
        echo $headBlock->getCssJsHtml();

        echo '<style type="text/css">
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

            echo '<ul id="profileRows">';

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

                    $showFinished = false;
                    $batchImportModel = $batchModel->getBatchImportModel();
                    $importIds = $batchImportModel->getIdCollection();
                    $countItems = count($importIds);

                    $batchConfig = array(
                        'styles' => array(
                            'error' => array(
                                'icon' => Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif'),
                                'bg'   => '#FDD'
                            ),
                            'message' => array(
                                'icon' => Mage::getDesign()->getSkinUrl('images/fam_bullet_success.gif'),
                                'bg'   => '#DDF'
                            ),
                            'loader'  => Mage::getDesign()->getSkinUrl('images/ajax-loader.gif')
                        ),
                        'template' => '<li style="#{style}" id="#{id}">'
                                    . '<img src="#{image}" class="v-middle" style="margin-right:5px"/>'
                                    . '<span class="text">#{text}</span>'
                                    . '</li>',
                        'text'     => $this->__('Processed <strong>%s%% %s/%d</strong> records', '#{percent}', '#{updated}', $countItems),
                        'successText'  => $this->__('Imported <strong>%s</strong> records', '#{updated}')
                    );
echo '
<script type="text/javascript">
var countOfStartedProfiles = 0;
var countOfUpdated = 0;
var countOfError = 0;
var importData = [];
var totalRecords = ' . $countItems . ';
var config= '.Zend_Json::encode($batchConfig).';
</script>
<script type="text/javascript">
function addImportData(data) {
    importData.push(data);
}

function execImportData() {
    if (importData.length == 0) {

        $("updatedRows").down("img").src = config.styles.message.icon;
        $("updatedRows").style.backgroundColor = config.styles.message.bg;
        new Insertion.Before($("liFinished"), config.tpl.evaluate({
            style: "background-color:"+config.styles.message.bg,
            image: config.styles.message.icon,
            text: config.tplSccTxt.evaluate({updated:(countOfUpdated-countOfError)}),
            id: "updatedFinish"
        }));
        new Ajax.Request("' . $this->getUrl('*/*/batchFinish', array('id' => $batchModel->getId())) .'", {
            onComplete: function() {
                $(\'liFinished\').show();
            }
        });
    } else {
        sendImportData(importData.shift());
    }
}

function sendImportData(data) {
    if (!config.tpl) {
        config.tpl = new Template(config.template);
        config.tplTxt = new Template(config.text);
        config.tplSccTxt = new Template(config.successText);
    }
    if (!$("updatedRows")) {
        new Insertion.Before($("liFinished"), config.tpl.evaluate({
            style: "background-color: #FFD;",
            image: config.styles.loader,
            text: config.tplTxt.evaluate({updated:countOfUpdated, percent:getPercent()}),
            id: "updatedRows"
        }));
    }
    countOfStartedProfiles++;

    new Ajax.Request("'.$this->getUrl('*/*/batchRun').'", {
      method: "post",
      parameters: data,
      onSuccess: function(transport) {
        countOfStartedProfiles --;
        countOfUpdated += data["rows[]"].length;
        if (transport.responseText.isJSON()) {
            addProfileRow(transport.responseText.evalJSON());
        } else {
            new Insertion.Before($("updatedRows"), config.tpl.evaluate({
                style: "background-color:"+config.styles.error.bg,
                image: config.styles.error.icon,
                text: transport.responseText.escapeHTML(),
                id: "error-" + countOfStartedProfiles
            }));
            countOfError += data["rows[]"].length;
        }
        execImportData();
      }
    });
}

function getPercent() {
    return Math.ceil((countOfUpdated/totalRecords)*1000)/10;
}

function addProfileRow(data) {
    if (data.errors.length > 0) {
        for (var i=0, length=data.errors.length; i<length; i++) {
            new Insertion.Before($("updatedRows"), config.tpl.evaluate({
                style: "background-color:"+config.styles.error.bg,
                image: config.styles.error.icon,
                text: data.errors[i],
                id: "id-" + (countOfUpdated + i + 1)
            }));
            countOfError ++;
        }
    }
    $("updatedRows").down(".text").update(config.tplTxt.evaluate({updated:countOfUpdated, percent:getPercent()}));

}
</script>
';


                    $jsonIds = array_chunk($importIds, 1);
                    foreach ($jsonIds as $part => $ids) {
                        $data = array(
                            'batch_id'   => $batchModel->getId(),
                            'rows[]'     => $ids
                        );
                        echo '<script type="text/javascript">addImportData('.Zend_Json::encode($data).')</script>';
                    }
                    echo '<script type="text/javascript">execImportData()</script>';
                    //print $this->getUrl('*/*/batchFinish', array('id' => $batchModel->getId()));
                }
                else {
                    $batchModel->delete();
                }
            }

            if ($showFinished) {
                echo "<script type=\"text/javascript\">$('liFinished').show();</script>";
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
