<?php

/**
 * Source code artefact block.
 *
 * @package mahara
 * @subpackage blocktype-artefact-code
 *
 * @copyright (c) 2012 The Development Manager Ltd
 * @author Luke Carrier <luke.carrier@tdm.info>
 */

defined("INTERNAL") || exit;

class PluginBlocktypeCode extends PluginBlocktype {
    // Supported languages
    // The auto-detect option is added in languagechooser_element()
    protected static $langs = array(
        "php",
        "python",
    );

    // List the available styles
    // Heavy-traffic sites would really suffer if we always scanned the styles
    // directory on every request
    protected static $styles = array(
        "dark",
        "monokai",
        "pojoaque",
        "zenburn",
    );

    // Multiple instances of this block are permitted on a single page
    public static function single_only() {
        return false;
    }

    // The title of the plugin is defined in the lang files
    public static function get_title() {
        return static::get_string("title");
    }

    // The description of the plugin is also defined in the lang files
    public static function get_description() {
        return static::get_string("description");
    }

    // Fits under the files, images and video category
    public static function get_categories() {
        return array("fileimagevideo");
    }

    // Get artefacts associated with a block instance
    public static function get_artefacts(BlockInstance $inst) {
        $data = $inst->get("configdata");
        return array_key_exists("artefactid", $data)
                ? array($data["artefactid"]) : false;
    }

    // Language selection UI options
    public static function languagechooser_element($default="auto") {
        $langs = array("auto" => static::get_string("language_auto"));
        foreach (static::$langs as $lang) {
            $langs[$lang] = static::get_string("language_{$lang}");
        }

        return array(
            "name"         => "language",
            "title"        => static::get_string("language"),
            "type"         => "select",
            "options"      => $langs,
            "rules"        => array(
                "required" => true,
            ),
            "defaultvalue" => $default,
        );
    }

    // Style selection UI options
    public static function stylechooser_element($default="zenburn") {
        $styles = array("zenburn" => static::get_string("style_zenburn"));
        foreach (static::$styles as $style) {
            $styles[$style] = static::get_string("style_{$style}");
        }

        return array(
            "name"         => "style",
            "title"        => static::get_string("style"),
            "type"         => "select",
            "options"      => $styles,
            "rules"        => array(
                "required" => true,
            ),
            "defaultvalue" => $default,
        );
    }

    // Artefact selection UI options
    public static function artefactchooser_element($default=NULL) {
        return array(
            "name"          => "artefactid",
            "title"         => static::get_string("artefact"),
            "type"          => "artefactchooser",
            "artefacttypes" => array("file"),
            "blocktype"     => "code",
            "rules"         => array(
                "required" => "true",
            ),
            "defaultvalue"  => $default,
        );
    }

    // List the JavaScript files the plugin requires to function
    public static function get_instance_javascript() {
        return array(
            "theme/raw/static/script/highlight.pack.js",
            "theme/raw/static/script/codeplugin.js",
        );
    }

    // Render an instance of the block as HTML
    public static function render_instance(BlockInstance $inst, $editing=false) {
        $data = $inst->get("configdata");

        $file = $inst->get_artefact_instance($data["artefactid"])->get_path();
        if (!file_exists($file)) {
            return NULL;
        }

        return '<script type="text/javascript">'
             .     'jQuery(document).ready(function($) {'
             .     '    window.code_plugin.loadStyle("' . $data["style"] . '");'
             .     '});'
             . '</script>'
             . '<pre>'
             .     '<code class="artefact-code lang-' . $data["language"] . ' style-' . $data["style"] . '">'
             .          clean_html(file_get_contents($file))
             .     '</code>'
             . '</pre>';
    }

    // No configuration yet
    public static function has_instance_config() {
        return true;
    }

    // Configuration dialog
    public static function instance_config_form(BlockInstance $inst) {
        $data     = $inst->get("configdata");
        $artefact = array_key_exists("artefactid", $data)
                     ? $data["artefactid"] : NULL;
        $style    = array_key_exists("style", $data)
                     ? $data["style"] : "zenburn";
        $language = array_key_exists("language", $data)
                     ? $data["language"] : "auto";

        return array(
            "language" => static::languagechooser_element($language),
            "style"    => static::stylechooser_element($style),
            "artefact" => static::artefactchooser_element($artefact),
        );
    }

    // How to handle Leap2A exports
    public static function default_copy_type() {
        return "full";
    }

    // Shorthand wrapper for get_string
    public static function get_string($key) {
        return get_string($key, "blocktype.file/code");
    }
}
