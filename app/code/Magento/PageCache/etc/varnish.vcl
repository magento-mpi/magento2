import std;

backend default {
    .host = "{{ host }}";
    .port = "{{ port }}";
}

acl purge {
{{ ips }}
}

sub vcl_recv {
    # prevent from gzipping on backend
    unset req.http.accept-encoding;

    if (req.restarts == 0) {
        if (req.http.x-forwarded-for) {
            set req.http.X-Forwarded-For =
            req.http.X-Forwarded-For + ", " + client.ip;
        } else {
            set req.http.X-Forwarded-For = client.ip;
        }
    }

    if (req.request == "PURGE") {
        if (client.ip !~ purge) {
            error 405 "Method not allowed";
        }
        if (req.http.X-Magento-Tags-Pattern) {
            ban("obj.http.X-Magento-Tags ~ " + req.http.X-Magento-Tags-Pattern);
        }
        ban("obj.http.X-Url ~ " + req.url);
        error 200 "Purged";
    }

    if (req.request != "GET" &&
        req.request != "HEAD" &&
        req.request != "PUT" &&
        req.request != "POST" &&
        req.request != "TRACE" &&
        req.request != "OPTIONS" &&
        req.request != "DELETE") {
          /* Non-RFC2616 or CONNECT which is weird. */
          return (pipe);
    }
    # We only deal with GET and HEAD by default
    if (req.request != "GET" && req.request != "HEAD") {
        return (pass);
    }

    if (req.url ~ "\.(css|js|jpg|png|gif|tiff|bmp|gz|tgz|bz2|tbz|mp3|ogg|svg|swf)(\?|$)") {
         unset req.http.Cookie;
    }

    return (lookup);
}

sub vcl_hash {
    if (req.http.cookie ~ "X-Magento-Vary=") {
        hash_data(regsub(req.http.cookie, "^.*?X-Magento-Vary=([^;]+);*.*$", "\1"));
    }
    {{ design_exceptions_code }}
}

sub vcl_hit {
    if (req.request == "PURGE") {
        purge;
        error 200 "Purged";
    }
}

sub vcl_miss {
    if (req.request == "PURGE") {
        purge;
        error 404 "Purged";
    }
}

sub vcl_fetch {
    if (req.url !~ "\.(jpg|png|gif|tiff|bmp|gz|tgz|bz2|tbz|mp3|ogg|svg|swf)(\?|$)") {
        set beresp.do_gzip = true;
        if (req.url !~ "\.(css|js)(\?|$)") {
            # set ttl from received Magento
            set beresp.ttl = std.duration(beresp.http.X-Magento-ttl + "s", 0s);
            set beresp.do_esi = true;
        }
    }

    # validate if we need to cache it and prevent from setting cookie
    # images, css and js are cacheable by default so we have to remove cookie also
    if (beresp.ttl > 0s && (req.request == "GET" || req.request == "HEAD")) {
        unset beresp.http.set-cookie;
    }

    # cache only successfully responses
    if (beresp.status != 200) {
        set beresp.ttl = 0s;
        return (hit_for_pass);
    }
    set beresp.http.X-Url = req.url;
}

sub vcl_deliver {
    unset resp.http.X-Magento-Tags;
    unset resp.http.X-Magento-ttl;
    unset resp.http.X-Url;
    unset resp.http.X-Powered-By;
    unset resp.http.Server;
    unset resp.http.X-Varnish;
    unset resp.http.Via;
    unset resp.http.Link;
}