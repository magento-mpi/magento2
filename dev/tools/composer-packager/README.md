composer-packager
=================

The tool that helps you pacakge magento source code into composer packages. 

This tool uses the information stored in etc/module.xml of each module, and create a composer package file out of it. 
The dependency's version are determined based on the module.xml file of those modules. The versions are modified to wrap upto 4 decimals.

Here are the steps that the tool would follow: 

1. Iterate through each Modules in /app/code/Magento folder, and create a Module class.
2. Insert the information about version.
3. Iterate through each dependencies, and check if Module Class exists for it. If so, add it to the dependency. 
4. If not, create one, and add it to dependency.
5. After iterating through all of the code, you should have an array of all modules with their versions and dependencies resolved.
6. Start creating composer package file, for each module, and insert all necessary information. 
7. Zip each module, and place it on /dev/composer-package/packages folder.

Installation Instructions
---
The source code is supposed to be copied over to 
<Magento Root>/dev/tools folder. 

After downloading the code to right place, you can run 
  php -f create-composer.php -- -v
  
This will create the folder 'packages' inside /dev/tools/composer-packager, and dump zip folders for each magento core modules located in /app/code/ folder.
