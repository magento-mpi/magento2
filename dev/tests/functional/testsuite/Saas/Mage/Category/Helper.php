<?php
/**
 * Redefines core helper in connection with change of the default theme
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Mage_Category_Helper extends Core_Mage_Category_Helper
{
    /**
     * OpenCategory
     *
     * WARNING: ONLY for visible(in menu) categories
     *
     * @param string $categoryPath
     */
    public function frontOpenCategory($categoryPath)
    {
        // Determine category title
        $nodes = explode('/', $categoryPath);
        $nodesReverse = array_reverse($nodes);
        $title = '';
        foreach ($nodesReverse as $key => $value) {
            $title .= $value;
            if (isset($nodes[$key + 1])) {
                $title .= ' - ';
            }
        }
        $this->addParameter('elementTitle', $title);
        // Form category xpath
        $link = "//nav[@class='navigation']/ul";
        foreach ($nodes as $node) {
            $link = $link . '/li/a[contains(./span,"' . $node . '")]';
        }
        $availableElement = $this->elementIsPresent($link);
        if (!$availableElement) {
            $this->fail('"' . $categoryPath . '" category page could not be opened');
        }
        // Determine category mca parameters
        $mca = $this->getMcaFromUrl($availableElement->attribute('href'));
        $this->_determineMcaParams($mca);
        $availableElement->click();
        $this->waitForPageToLoad();
        $this->validatePage();
    }
}
