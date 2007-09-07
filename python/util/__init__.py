__all__ = ["config", "modifier", "search"]
__help__ = """Usage: python magento-util.py <DIRECTORY TO SCAN> [option]
Avaliable options:
    -f (--find-orphaned) : Same as '--insert-license', but using this option program will put
                           'LICENSE OF NOTICE' only to files wich not consists it yet
    --insert-license     : Use this option to put 'LICENSE OF NOTICE' to each file (according to config options)
    --scan-phrases       : Use this option to scan phrases for translation
    --help               : Display this help and exit"""
