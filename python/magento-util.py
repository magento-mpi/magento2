import sys, os
import util
from util.config import *
from util.search import *
from util.modifier import *
from util.parser import *

search = Search()
modifier = Modifier()
parser = Parser()

def prepare():
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

try: 
    os.environ['BASE_DIR'] = sys.argv[1]
except:
    print 'Invalid BASE directory\n'
    sys.exit(util.__help__)

try:
    param = sys.argv[2]
except:
    param = '--insert-license'

if param == '--help':
    sys.exit(util.__help__)
if param == '-f':
    prepare()

    print 'Searching for orphaned files... Please wait.'
    search.findOrphaned()
    if len(search.files) > 0:
        _continue = 'Y'
        print 'Found ', len(search.files), ' files'
        _userChoise = raw_input("Would you like to insert NOTICE OF LICENSE to this files? (Y/N) [Y]: ")
        if _userChoise:
            _continue = _userChoise

        if _continue == 'Y':
            modifier.modify(search.files)
        else:
            sys.exit('No changes was made.')
    sys.exit('\nDONE')
if param == '--insert-license':
    prepare()
    raw_input("Now, this software will insert NOTICE OF LICENSE to each founded file.\nPress ENTER to continue or ^C to abort...")
    print "Please wait, operation may take a few minutes.\n"

    modifier.modify(search.files)
    print "Files successfully changed\n"
if param == '--scan-phrases':
    prepare()
    raw_input("Now, this software will parse PHRASES in each founded file.\nPress ENTER to continue or ^C to abort...")
    print "Please wait, operation may take a few minutes.\n"
    parser.setPatterns(phrasePatterns).search(search.files)
    print "\nDONE"
else:
    print "Invalid option '" + param + "'\n"
    sys.exit(util.__help__)
