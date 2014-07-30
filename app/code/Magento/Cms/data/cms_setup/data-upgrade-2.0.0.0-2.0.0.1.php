<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\Cms\Model\Resource\Setup $this */

$idVersusLayout = [
    'one_column' => '1column',
    'two_columns_left' => '2columns-left',
    'two_columns_right' => '2columns-right',
    'three_columns' => '3columns'
];

foreach ($this->getPageCollection() as $page) {
    if ($page->getRootTemplate() && isset($idVersusLayout[$page->getRootTemplate()])) {
        $page->setRootTemplate($idVersusLayout[$page->getRootTemplate()]);
    }
    if ($page->getCustomRootTemplate() && isset($idVersusLayout[$page->getCustomRootTemplate()])) {
        $page->setCustomRootTemplate($idVersusLayout[$page->getCustomRootTemplate()]);
    }
    $page->save();
}
