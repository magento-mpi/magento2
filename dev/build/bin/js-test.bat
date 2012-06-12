del ..\..\tests\js\test-output\*.* /Q
rmdir ..\..\tests\js\test-output
md ..\..\tests\js\test-output
java -jar ..\..\build\bin\jsTestDriver.jar --config ..\..\tests\js\jsTestDriver.conf --port 9876 --browser "C:\Program Files (x86)\Mozilla Firefox\firefox.exe" --tests all --testOutput ..\..\tests\js\test-output
