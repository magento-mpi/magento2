function $import(path){
    var i, base, scripts = document.getElementsByTagName("script");
    var script;
    for (i=0; i<scripts.length; i++) {
        if (scripts[i].src.match(path)) {
            return true;
        }
    }
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = path;
    document.getElementsByTagName('head')[0].appendChild(script);
}