import os, sys, csv
from config import *

class Parser:

    matches     = {}
    files       = {}
    patterns    = None
    outputDir   = './.output'
    foundFiles  = None

    def __init__(self):
        if not os.path.exists(self.outputDir):
            try:
                os.mkdir(self.outputDir)
            except (RuntimeError):
                print 'Unable to create output directory.'
                sys.exit(RuntimeError)
        return

    def search(self, files):
        self.foundFiles = files
        for file in files:
            file = os.path.abspath(file)
            (root, ext) = os.path.splitext(file)
            lines = open(file, 'r').readlines()
            lineNumber = 0

            for line in lines:
                lineNumber += 1
                for pattern in self.patterns:
                    phrases = pattern.findall(line)
                    if phrases:
                        moduleName = self.getModuleName(file)
                        if not self.matches.has_key(moduleName):
                            self.matches[moduleName] = {}

                        for phrase in phrases:
                            if len(phrase) > 0:
                                fileData = {}
                                fileData['filename'] = file
                                fileData['line'] = lineNumber

                                self.files[phrase] = fileData
                                self.addPhrase(phrase, moduleName)

        self.writePhrases().writeFiles()
        return self

    def addPhrase(self, phrase, moduleName):
        self.matches[moduleName][phrase] = phrase
        return self

    def setPatterns(self, patterns):
        self.patterns = patterns
        return self

    def writeFiles(self):
        string = ''
        for file in self.files:
            string += file + '\t' + str(self.files[file]['filename']) + ' : ' + str(self.files[file]['line']) + '\n'

        f = open(self.outputDir + "/files.txt", "wb")
        f.write(string)
        f.close()
        return self

    def writePhrases(self):
        matches = []
        csv.register_dialect('excel', CsvSpace())
        for module in self.matches:
            outputDir = self.getModuleDir(module) + '/' + defTranslationDirName + '/' + defLanguageDirName;
            if not os.path.exists(outputDir):
                try:
                    os.makedirs(outputDir)
                except (RuntimeError):
                    print 'Unable to create output directory.'
                    sys.exit(RuntimeError)
            for phrase in self.matches[module]:
                matches.append((phrase, self.matches[module][phrase]))
                writer = csv.writer(open(outputDir + "/phrases.csv", "wb"), 'excel')
                writer.writerows(matches)
            matches = []
        return self

    def getModuleName(self, file):
        for dir in langScanDirectories:
            s = re.findall("^(.*?)"+dir+"(.*?)$", file)
            if s :
                parts = re.split("\/|\.", s[0][1])
        return parts[0]
      
    def getModuleDir(self, moduleName):
        for dir in langScanDirectories:
            modDir = os.path.abspath(os.environ['BASE_DIR']+dir+moduleName)
            if os.path.exists(modDir):
                return modDir
        raise "Unable to find '"+moduleName+"' module dir."

class CsvSpace(csv.Dialect):
    delimiter        = ";"
    lineterminator   = '\n'
    doublequote      = True
    skipinitialspace = False
    quoting          = csv.QUOTE_ALL
    quotechar        = '"'
