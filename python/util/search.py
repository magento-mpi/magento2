import os, fileinput, string
from config import *

class Search:
    
    __pathes = []
    __filetypes = []
    files = []

    def __init__(self):
        pass

    def addPath(self, path):
        self.__pathes.append(path)

    def addFileType(self, filetype):
        self.__filetypes.append(filetype)

    def prepare(self):
        fileMask = []
        for ext in self.__filetypes:
            tmpVar = str('-iname *' + ext)
            fileMask.append(tmpVar)

        j = 0
        dirList = {}
        for path in self.__pathes:
            cmd = str('find ' + os.environ['BASE_DIR'] + path + ' \( ' + ' -or '.join(fileMask) + ' \) -print ')
            i = 0
            for file in os.popen(cmd).readlines():
                i += 1
                self.files.append(file[:len(file)-1])
                dirList[path] = i
            j += i

        print "Total files: ", j, '\n'

        for dir in dirList:
            print dirList[dir], '\t', dir
        print "\n\n"

    def findOrphaned(self):
        tmpFiles = []
        for file in self.files:
            f = open(file, 'r')
            lineno = 0
            found = 0
            for line in f.readlines():
                if found > 0:
                    continue
                lineno = string.find(line, checkString)
                if lineno > 0:
                    found = 1
            if found == 0:
                print file
                tmpFiles.append(file)
            f.close()
        self.files = tmpFiles
        return
