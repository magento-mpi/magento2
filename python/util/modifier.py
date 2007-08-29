import os, sys, fileinput, string, re
from config import *

class Modifier:

    __package = ''
    __category = ''
    __classTemplate = ''

    def __init__(self):
        pass

    def modify(self, files):
        for file in files:
            (root, ext) = os.path.splitext(file)

            template = self._prepareTemplate(templates[ext], file)
            if ext == '.php':
                for line in fileinput.input(file,inplace=1):

                    self.__classTemplate = ' * @category   ' + self.__category + '\n * @package    ' + self.__package + '\n'

                    line = re.sub('\s+\*\s+\@package(.*?)\n', self.__classTemplate, line)
                    line = re.sub('\s+\*\s+\@subpackage(.*?)\n', '', line)
                    line = re.sub('\s+\*\s+\@license(.*?)\n', '', line)
                    line = re.sub('\s+\*\s+\@copyright(.*?)\n', '', line)

                    lineno = 0
                    lineno = string.find(line, '<?')
                    if lineno == 0:
                        line = line.replace('<?php', template)

                    sys.stdout.write(line)

            if ext == '.xml':
                for line in fileinput.input(file,inplace=1):
                    lineno = 0
                    lineno = string.find(line, '<?xml version="1.0"?>')
                    if lineno == 0:
                        line = line.replace('<?xml version="1.0"?>', template)
                    sys.stdout.write(line)

            if ext == '.phtml' or ext == '.css' or ext == '.js':
                f = open(file, 'r')
                lines = f.readlines()
                lines.insert(0, template)
                f.close()

                f = open(file, 'w')
                f.writelines(lines)
                f.close()
        return

    def _prepareTemplate(self, template, file):
        for dir in scanDirectories:
            s = re.findall("^(.*?)"+dir+"(.*?)$", file)
            if s :
                package = scanDirectories[dir][0]
                if re.match("(.*?)_\*", package):
                    parts = re.split("\/|\.", s[0][1])
                    package = re.sub('\*', parts[0], package)
                template = re.sub("\{\{category\}\}", scanDirectories[dir][1], template)
                template = re.sub("\{\{package\}\}", package, template)

                self.__package = package
                self.__category = scanDirectories[dir][1]
        return template
