#!/bin/sh

cd "$4"

#Fix file in all version
find . -name "*.html" | xargs sed -i "s~#MAGE_VERSION#~$1~g"

#Fix text and titles in Varien Files
find ./Varien -name "*.html" | xargs sed -i "s~#MAGE_TITLE#~$2~g"
find ./Varien -name "*.html" | xargs sed -i "s~#MAGE_LINK_TITLE#~$3~g"
find ./Varien -name "*.html" | xargs sed -i "s~#MAGE_LINK_TEXT#~$3~g"
find . -path "./Varien/*/*" -prune -name "*.html" | xargs sed -i "s~#MAGE_LINK_HREF#~../../index.html~g"
find . -path "./Varien/*" -prune -name "*.html" | xargs sed -i "s~#MAGE_LINK_HREF#~../index.html~g"

#Fix text and titles in Mage Files
find . -name "*.html" | grep -v "/Varien/" | xargs sed -i "s~#MAGE_TITLE#~$3~g"
find . -name "*.html" | grep -v "/Varien/" | xargs sed -i "s~#MAGE_LINK_TITLE#~$2~g"
find . -name "*.html" | grep -v "/Varien/" | xargs sed -i "s~#MAGE_LINK_TEXT#~$2~g"
find . -path "./*/*" -prune -name "*.html" | xargs sed -i "s~#MAGE_LINK_HREF#~../Varien/index.html~g"
find . -path "./*" -prune -name "*.html" | xargs sed -i "s~#MAGE_LINK_HREF#~Varien/index.html~g"

exit 0
