require([
    "sparta-core"
], function(atto) {

    // TODO: bootstrap the tag cloud
    atto.xhrRequest({
        url: "tags",
        handleAs: "json",
        preventCache: true,
        success: function(data) {
            //var cloud = TagCloud(data, window.baseUrl, 0, 0.75, 1.2, ' ', false, "__BASEURL__/search?tag=__TAGNAME__");
            var t, a, links = document.createDocumentFragment(), tagdata = JSON.parse(data);
            for (t in tagdata) {
                a = document.createElement('a');
                a.href = atto.supplant("{url}/search?tag={tag}", {url:window.baseUrl,tag:tagdata[t]});
                a.text = tagdata[t];
                links.appendChild( a );
                links.appendChild( document.createTextNode(' ') );
            }

            atto.byId('cloud_parent').appendChild( links );
        },
        failure: function(error) {
            atto.byId('cloud_parent').innerHTML = "An unexpected error occurred: " + error;
        }
    });

    // TODO: syntax highlighting of any related pre/code blocks
    //SyntaxHighlighter.all();
});
