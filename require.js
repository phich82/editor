function load(url)
{
    var ajax = new XMLHttpRequest();
    ajax.open('GET', url, false);
    ajax.onreadystatechange = function () {
        var script = ajax.response || ajax.responseText;
        if (ajax.readyState === 4) {
            switch(ajax.status) {
                case 200:
                    eval.apply( window, [script] );
                    console.log("library loaded: ", url);
                    break;
                default:
                    console.log("ERROR: library not loaded: ", url);
            }
        }
    };
    ajax.send(null);
}

function require$(url) {
    $.ajax({
        url: url,
        dataType: "script",
        async: false,           // <-- This is the key
        success: function () {
            console.log("library loaded: ", url);
        },
        error: function () {
            throw new Error("Could not load script: " + url);
        }
    });
}