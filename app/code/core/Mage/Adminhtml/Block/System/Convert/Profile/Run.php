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
    protected $_inventoryFields = array(
        'qty', 'min_qty', 'use_config_min_qty',
        'is_qty_decimal', 'backorders', 'use_config_backorders',
        'min_sale_qty','use_config_min_sale_qty','max_sale_qty',
        'use_config_max_sale_qty','is_in_stock'

    );

    public function getProfile()
    {
        return Mage::registry('current_convert_profile');
    }

    protected function _toHtml()
    {
        $profile = $this->getProfile();

        echo '<html><head>
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
            echo $this->__("Warning: Please don't close window during importing data");
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
        }

        $sessionId = Mage::registry('current_dataflow_session_id');
        $import = Mage::getResourceModel('dataflow/import');
        $total = $import->loadTotalBySessionId($sessionId);
        echo '<li>
    <div style="position:relative">
        <div id="progress_bar" style="position:absolute; background:green; height:2px; width:0; top:-2px; left:-2px; overflow:hidden; "></div>
        <div>'.$this->__('Total records: %s', '<strong>'.$total["cnt"].'</strong>').', '.$this->__('Processed records: %s', '<strong><span id="records_processed">0</span></strong>').', '.$this->__('ETA: %s', '<strong><span id="finish_eta">N/A</span></strong>').'</div>
    </div>
</li>
<script type="text/javascript">
function update_progress(idx, time) {
    var total_rows = '.$total['cnt'].';
    var elapsed_time = time-'.time().';
    var total_time = Math.round(elapsed_time*total_rows/idx);
    var eta = total_time-elapsed_time;
    var eta_hours = Math.floor(eta/3600);
    var eta_minutes = Math.floor(eta/60)%60;
    document.getElementById("records_processed").innerHTML= idx;
    document.getElementById("finish_eta").innerHTML = (eta_hours ? eta_hours+" '.$this->__('hour(s)').'" : "")+" "+(eta_minutes ? eta_minutes+" '.$this->__('minute(s)').'" : "");
    document.getElementById("progress_bar").style.width = (idx/total_rows*100)+"%";
}
</script>';

        $importData = Mage::getModel('dataflow/import');
        $product = Mage::getModel('catalog/product');
        $stockItem = Mage::getModel('cataloginventory/stock_item');
        $idx = 0;
        while ($total['min'] && $total['min'] < $total['max']) {
            $data = $import->loadBySessionId($sessionId, $total['min'], $total['max']);
            if (!$data) {
                break;
            }
            foreach($data as $rowStr) {
                $total['min'] = $rowStr['import_id'];
                $row = unserialize($rowStr['value']);

                echo '<script>update_progress('.(++$idx).', '.time().');</script>';

                set_time_limit(240);
                $product->importFromTextArray($row)->save();
                if ($stockItem) {
                    $stockItem->unsetData();
                    $stockItem->loadByProduct($product);
                    if (!$stockItem->getId()) {
                        $stockItem->setProductId($product->getId())->setStockId(1);
                    }
                    foreach ($row as $field=>$value) {
                        if (in_array($field, $this->_inventoryFields)) {
                            $stockItem->setData($field, $value);
                        }
                    }
                    $stockItem->save();
                }

                $importData->setImportId($total['min'])->setStatus(1)->save();
            }
            unset($data);

            $total = $import->loadTotalBySessionId($sessionId);
        }
        unset($importData, $product, $stockItem);

        echo '<li>';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/note_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
        echo $this->__("Finished profile execution.");
        echo '</li>';
        echo "</ul>";

        echo '</body></html>';
        exit;
    }
}
