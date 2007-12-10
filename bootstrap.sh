#!/bin/bash

MAGE_SVN_ROOT=http://svn.magentocommerce.com/svn/magento/base/magento/trunk

for ENTRY in index.php pear LICENSE.txt favicon.ico .htaccess lib app/etc app/Mage.php app/code/core/Mage/Core app/code/core/Mage/Install js/prototype js/mage js/varien js/spacer.gif js/blank.html app/design/install skin/install
do
  #echo $ENTRY
  svn export --force $MAGE_SVN_ROOT/$ENTRY ./$ENTRY
done

