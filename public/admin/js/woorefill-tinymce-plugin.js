(function() {
    tinymce.create("tinymce.plugins.woorefill", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            //add new button
            ed.addButton("quick_refill", {
                title : "QuickRefill Widget",
                cmd : "quick_refill_command",
                image : "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkJz48c3ZnIGhlaWdodD0iMS4wNDE2N2luIiBzdHlsZT0ic2hhcGUtcmVuZGVyaW5nOmdlb21ldHJpY1ByZWNpc2lvbjsgdGV4dC1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyBpbWFnZS1yZW5kZXJpbmc6b3B0aW1pemVRdWFsaXR5OyBmaWxsLXJ1bGU6ZXZlbm9kZDsgY2xpcC1ydWxlOmV2ZW5vZGQiIHZlcnNpb249IjEuMSIgdmlld0JveD0iMCAwIDIwMSAyMDEiIHdpZHRoPSIxLjA0MTY3aW4iIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+CiAgIDwhW0NEQVRBWwogICAgLmZpbDEge2ZpbGw6I0ZFRkVGRX0KICAgIC5maWwwIHtmaWxsOiMyREMxMDB9CiAgIF1dPgogIDwvc3R5bGU+PC9kZWZzPjxnIGlkPSJMYXllcl94MDAyMF8xIj48ZyBpZD0iXzMyMDUwMTgyNCI+PHJlY3QgY2xhc3M9ImZpbDAiIGhlaWdodD0iMjAxIiByeD0iMTkiIHJ5PSIxOSIgd2lkdGg9IjIwMSIvPjxnPjxwYXRoIGNsYXNzPSJmaWwxIiBkPSJNMTA3IDM0YzM3LDUgNTQsMjMgNTksNjAgMCwwIDAsMCAwLDAgMCw0IDAsOCA0LDggNCwwIDQsLTMgNCwtNiAwLDAgMCwtMSAwLC0yIDEsLTM1IC0zMCwtNjcgLTY2LC02OCAtMywwIC05LC0yIC05LDQgMCw0IDUsMyA4LDR6Ii8+PHBhdGggY2xhc3M9ImZpbDEiIGQ9Ik0xMTQgNDRjLTQsLTEgLTgsLTIgLTksMiAtMSw2IDQsNSA4LDYgMjMsNSAzMSwxMyAzNSwzNiAwLDEgMCwyIDAsMyAwLDIgMSw1IDUsNCAyLDAgMywtMSAzLC0zIDAsLTEgMCwtMyAwLC00IDAsLTIyIC0xOSwtNDIgLTQyLC00NHoiLz48cGF0aCBjbGFzcz0iZmlsMSIgZD0iTTExNiA2MWMtMiwwIC01LDEgLTUsMyAtMiw0IDEsNSA0LDUgOSwyIDE0LDcgMTUsMTYgMCwyIDEsMyAxLDQgMSwwIDIsMSA0LDEgMCwwIDEsLTEgMSwtMSAyLC0xIDIsLTMgMiwtNSAwLC0xMSAtMTIsLTIzIC0yMiwtMjN6Ii8+PHBhdGggY2xhc3M9ImZpbDEiIGQ9Ik0xNjQgMTM0Yy01LC00IC0xMCwtNyAtMTQsLTEwIC0xMCwtNyAtMTksLTcgLTI3LDMgLTQsNiAtOSw3IC0xNSw0IC0xNywtNyAtMzAsLTE5IC0zNywtMzUgLTEsLTMgLTIsLTUgLTIsLTggLTEsLTQgMSwtOCA2LC0xMSA0LC0zIDgsLTYgOCwtMTIgMCwtOCAtMjAsLTM0IC0yNywtMzcgLTMsLTEgLTYsLTEgLTEwLDAgLTE4LDYgLTI1LDIxIC0xOCwzOCA0LDkgOSwxOCAxNCwyNiAyMiwzNyA1NCw2NCA5Niw4MSAzLDIgNiwyIDcsMyAxMiwwIDI2LC0xMSAzMCwtMjIgMywtMTEgLTUsLTE1IC0xMSwtMjB6Ii8+PC9nPjwvZz48L2c+PC9zdmc+"
            });

            //button functionality.
            ed.addCommand("quick_refill_command", function() {
                ed.execCommand("mceInsertContent", 0, "[woorefill_quickrefill]");
            });

        },
        getInfo : function() {
            return {
                longname : "WooRefill Buttons",
                author : "Rafael SR",
                version : "1.0"
            };
        }
    });

    tinymce.PluginManager.add("woorefill", tinymce.plugins.woorefill);
})();