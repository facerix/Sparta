//
// This is basically a super-stripped-down, non-AMD version of Atto Core;
//   it only includes those functions needed to keep this project dependancy-free.
//

if (!window.atto) {
    var _XMLHttpFactories = [
        function () {return new XMLHttpRequest()},
        function () {return new ActiveXObject("Msxml2.XMLHTTP")},
        function () {return new ActiveXObject("Msxml3.XMLHTTP")},
        function () {return new ActiveXObject("Microsoft.XMLHTTP")}
    ];

    function _createXMLHTTPObject() {
        var xmlhttp = false;
        for (var i=0; i < _XMLHttpFactories.length; i++) {
            try {
                xmlhttp = _XMLHttpFactories[i]();
            }
            catch (e) {
                continue;
            }
            break;
        }
        return xmlhttp;
    }

    function _isArray(it) {
        // shamelessly lifted from Dojo Base)
        return it && (it instanceof Array || typeof it === "array");
    }

    function _colonSplit(s) {
        return s ? s.split(':') : null;
    }

    function _parse_args(arglist) {
        var args = [];
        if (arglist) {
            if (typeof arglist === 'string') {
                args = arglist.split(',').map(String.trim).map(_colonSplit); // split by comma, trim whitespace, then split by colon
            } else if (_isArray(arglist)) {
                args = arglist.map(_colonSplit);
            } else if (typeof arglist === 'object') {
                for (var k in arglist) {
                    args.push([k, arglist[k]]);
                }
            }
        }
        return args;
    }

    function _args_mixin(old_args, new_arglist) {
        var new_args = _parse_args(new_arglist), key, val;
        for (var i in new_args) {
            key = new_args[i][0];
            val = new_args[i][1];
            old_args[key] = val;
        }
        return old_args;
    }  // --> this is the one that gets exposed


    window.atto = {
        on: function(tgt, type, func, useCapture) {
            // follows the API of the standard addEventListener, but abstracts it to work cross-browser
            var capture = useCapture || false;
            if (tgt.addEventListener) {
                // modern standards-based browsers
                tgt.addEventListener(type, func, capture);
            } else if (tgt.attachEvent) {
                // IE < 9
                tgt.attachEvent('on'+type, func);
            } else if (typeof tgt['on'+type] !== 'undefined') {
                // old school (can assign to the element's event handler this way, provided it's not undefined)
                var oldfunc = tgt['on'+type];
                if (typeof oldfunc === 'function') {
                    tgt['on'+type] = function() { oldfunc(); func(); };
                } else {
                    tgt['on'+type] = func;
                }
            } else {
                alert ("Can't add this event type: " + type + " to this element: " + tgt);
                return;
            }
            //console.log("Successfully bound "+type+" event to "+tgt);
        },

        formToObject: function(theForm) {
            return {};
        },

        xhrRequest: function(args) {
            var opts = _args_mixin({
                url: '',
                postData: '',
                success: null,
                failure: null
            }, args);

            var req = _createXMLHTTPObject();
            if (!req) return;
            if (opts.postData) {
                req.open('POST', opts.url, true);
                req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
            } else {
                req.open('GET', opts.url, true);
            }
            req.onreadystatechange = function () {
                if (req.readyState != 4) return;
                if (req.status != 200 && req.status != 304) {
                    //alert('HTTP error ' + req.status);
                    if (opts.failure && typeof opts.failure === 'function') {
                        //console.debug(opts.failure);
                        opts.failure.call(this,req);
                    }
                    return;
                }
                if (opts.success && typeof opts.success === 'function') {

                    opts.success.call(this, req.response || req.responseText);
                }
            }
            if (req.readyState == 4) return;
            req.send(opts.postData);
        }
    } // end of window.atto definition
} // end of if(!window.atto) block
