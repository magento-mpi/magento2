#!/bin/sh

MAGE_SVN_ROOT=http://svn.magentocommerce.com/svn/magento/base/magento/trunk
MAGE_LOCAL_ROOT="."

for ENTRY in index.php pear LICENSE.txt favicon.ico lib app/etc app/Mage.php app/code/core/Mage/Core app/code/core/Mage/Install js/prototype js/mage js/varien js/spacer.gif js/blank.html app/design/install app/locale/*/Mage_Install.* app/locale/*/Mage_Core.* skin/install */.htaccess
do
  #echo $ENTRY
  svn export --force $MAGE_SVN_ROOT/$ENTRY $MAGE_LOCAL_ROOT/$ENTRY
done

mkdir $MAGE_LOCAL_ROOT/media
chmod 777 -R $MAGE_LOCAL_ROOT
