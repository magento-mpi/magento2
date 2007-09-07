import os, sys, csv
from config import *

class Parser:

    matches     = {}
    files       = {}
    patterns    = None
    outputDir   = './.output'

    def __init__(self):
        if not os.path.exists(self.outputDir):
            try:
                os.mkdir(self.outputDir)
            except (RuntimeError):
                print 'Unable to create output directory.'
                sys.exit(RuntimeError)
        return

    def search(self, files):
        for file in files:
            file = os.path.abspath(file)
            (root, ext) = os.path.splitext(file)
            lines = open(file, 'r').readlines()
            lineNumber = 0
            moduleName = self.getModuleName(file)

            if not self.matches.has_key(moduleName):
                self.matches[moduleName] = []

            for line in lines:
                lineNumber += 1
                for pattern in self.patterns:
                    phrases = pattern.findall(line)
                    if phrases:
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
        """
        for module in self.matches:
            if self.matches[module]:
                del(self.matches[module][phrase])
                self.matches[module][module+':'+phrase] = phrase
                self.matches[moduleName].append((module+':'+phrase, phrase))
            else:
                self.matches[moduleName].append((phrase, phrase))
        """
        self.matches[moduleName].append((phrase, phrase))
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
        for module in self.matches:
            for phrase in self.matches[module]:
                matches.append(phrase)

        csv.register_dialect('excel', CsvSpace())
        writer = csv.writer(open(self.outputDir + "/phrases.csv", "wb"), 'excel')
        writer.writerows(matches)
        return self

    def getModuleName(self, file):
        for dir in scanDirectories:
            s = re.findall("^(.*?)"+dir+"(.*?)$", file)
            if s :
                parts = re.split("\/|\.", s[0][1])

        return parts[0]
       

class CsvSpace(csv.Dialect):
    delimiter        = ";"
    lineterminator   = '\n'
    doublequote      = True
    skipinitialspace = False
    quoting          = csv.QUOTE_ALL
    quotechar        = '"'
