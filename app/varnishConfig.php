<?php
echo "


## For varies based on accept-encoding - possibly to change to this scheme ther part below that makes everything gzipped
if (req.http.Accept-Encoding) {
    if (req.url ~ \"\\.(jpg|png|gif|gz|tgz|bz2|tbz|mp3|ogg)$\") {
        # No point in compressing these
        remove req.http.Accept-Encoding;
    } elsif (req.http.Accept-Encoding ~ \"gzip\") {
        set req.http.Accept-Encoding = \"gzip\";
    } elsif (req.http.Accept-Encoding ~ \"deflate\") {
        set req.http.Accept-Encoding = \"deflate\";
    } else {
        # unknown algorithm
        remove req.http.Accept-Encoding;
    }
}
";



echo "
## default configuration
backend default {
    .host = \"127.0.0.1\"; # cheange to host ip
    .port = \"80\";
}

## Pass all non-cache-able request types
sub vcl_recv {
    if (req.request != \"GET\" && req.request != \"HEAD\") {
        ## Varnish should remove all cookies from cached objects
        return(pass);
    }
}

sub vcl_hash {
    hash_data(req.url);
    if (req.http.host) {
        hash_data(req.http.host);
    } else {
        hash_data(server.ip);
    }
    return (hash);
}

## Purge requests
sub vcl_hit {
    if (req.request == \"PURGE\") {
        purge;
        error 200 \"Purged.\";
        }
}

sub vcl_miss {
    if (req.request == \"PURGE\") {
        purge;
        error 200 \"Purged.\";
    }
}


#pass client ip address
sub vcl_recv {
    remove req.http.X-Forwarded-For;
    set req.http.X-Forwarded-For = client.ip;
}

##enable gzip for all (?! should we do it for images against varnish recommendations?
sub vcl_fetch {
  set beresp.do_gzip = true;
}

## Access list for backend should be inroduced
acl admin {
    \"192.168.0.1\" ##change to ip allowed to access backend here
}

sub vcl_recv {
    if (req.url ~ /backend/) {
        if (client.ip ~ admin) {
           return(pass);
        }
        error 403 \"Forbidden.\";
    }
}

## Objects TTL should be received from http headers
sub vcl_fetch {
    if (beresp.http.X-VARNISH-TTL) {
      C{
        char *ttl;
        /* first char in third param is length of header plus colon in octal */
        ttl = VRT_GetHdr(sp, HDR_BERESP, \"\016X-VARNISH-TTL:\");
        VRT_l_beresp_ttl(sp, atoi(ttl));
      }C
      remove beresp.http.X-VARNISH-TTL;
      return (deliver);
    }

    # If response has no Cache-Control/Expires headers, Cache-Control: no-cache, or Cache-Control: private, don't cache
    if ( (!beresp.http.Cache-Control && !beresp.http.Expires) || beresp.http.Cache-Control ~ \"no-cache\" || beresp.http.Cache-Control ~ \"private\" ) {
      return (pass);
    }
    #esi processing
    if ( beresp.http.X-RUN-ESI ) {
        esi;
        remove beresp.http.X-RUN-ESI;
    }
}
## cache images
if ( req.url ~ \"\\.(png|gif|jpg|css|js|ico)\" ) {
     set beresp.ttl = 30m;
     return (deliver);
  }

## X_MAGETNO_VARY cookie
sub vcl_hash {
    hash_data(req.http.cookie.X_MAGENTO_VARY)
}

";