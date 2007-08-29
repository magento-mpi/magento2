import sys, os
from util.config import *
from util.search import *
from util.modifier import *

search = Search()
modifier = Modifier()

try: 
    os.environ['BASE_DIR'] = sys.argv[1]
except:
    sys.exit('Error. Invalid BASE directory')

try:
    print "Preparing list of direstories to scan..."
    for path in scanDirectories:
        search.addPath(path)
    print "DONE\n\n"
except:
    sys.exit('Error. Unable to prepare the list of direstories.')

try:
    print "Preparing list of filetypes to scan..."
    for filetype in templates:
        search.addFileType(filetype)
    print "DONE\n\n"
except:
    sys.exit('Error. Unable to prepare the list of filetypes.')

raw_input("Now, this software will collect the list of files to be modified.\nPress ENTER to continue or ^C to abort...")
print "Please wait, operation may take a few minutes.\n"

search.prepare()

raw_input("Now, this software will insert NOTICE OF LICENSE to each founded file.\nPress ENTER to continue or ^C to abort...")
print "Please wait, operation may take a few minutes.\n"

modifier.modify(search.files)
print "Files successfully changed\n"
