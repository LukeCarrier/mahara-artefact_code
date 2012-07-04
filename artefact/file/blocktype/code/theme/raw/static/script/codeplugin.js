/**
 * Source code artefact block.
 *
 * @package mahara
 * @subpackage blocktype-artefact-code
 *
 * @copyright (c) 2012 The Development Manager Ltd
 * @author Luke Carrier <luke.carrier@tdm.info>
 */

;

var CodePlugin = function($) {
    this.$ = $;
    this.style_baseurl = window.config["wwwroot"]
                       + "/artefact/file/blocktype/code/theme/raw/static/style";
    this.style_loaded  = [];

    $("pre code.artefact-code").each(function(i, e) {
        hljs.highlightBlock(e);
    });
};

// Load a style set
CodePlugin.prototype.loadStyle = function(style) {
    var link, url;
    var $ = this.$;

    if (style in this.style_loaded) {
        return;
    }

    url = this.style_baseurl + "/" + style + ".css";

    $("head").append("<link />");
    link = $("head").children(":last");
    link.attr({
        rel: "stylesheet",
        type: "text/css",
        href: url
    });
};

jQuery(document).ready(function($) {
    window.code_plugin = new CodePlugin($);
});
