backend default {
    .host = "{{ host }}";
    .port = "{{ port }}";
}

sub vcl_recv {
    if (req.restarts == 0) {
        if (req.http.x-forwarded-for) {
            set req.http.X-Forwarded-For =
            req.http.X-Forwarded-For + ", " + client.ip;
        } else {
            set req.http.X-Forwarded-For = client.ip;
        }
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
    if (req.request != "GET" && req.request != "HEAD") {
       /* We only deal with GET and HEAD by default */
        return (pass);
    }

    # Cache images, styles, scripts
    if (req.url ~ "\.(jpg|png|gif|tiff|bmp|css|js)(\?|$)") {
        return(lookup);
    }
    return (lookup);
}

sub vcl_hash {
    hash_data(req.url);
    if (req.http.host) {
        hash_data(req.http.host);
    } else {
        hash_data(server.ip);
    }
    if (req.http.cookie ~ "X-Magento-Vary=") {
        hash_data(regsub(req.http.Cookie, "(^|.* )X-Magento-Vary=([^;]+)(;.*|$)", "\2"));
        remove req.http.Cookie;
    }
    return (hash);
}

sub vcl_fetch {
    if (beresp.status == 200) {
        return (deliver);
    }
}
