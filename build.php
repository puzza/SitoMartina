<?php

const DIR_OUT = 'compiled';
const DIR_IN = 'implementation';
const DIR_HTML = DIR_IN . '/html';
const DIR_CSS = DIR_IN . '/css';
const DIR_JS = DIR_IN . '/js';
const DIR_IMGS = DIR_IN . '/imgs';
const CONFIG_FILE = DIR_IN . '/config.json';

main();

function main()
{
    $config = json_decode(file_get_contents(CONFIG_FILE), true);

    delete_dir(DIR_OUT);
    copy_dir(DIR_JS, DIR_OUT . '/js');
    copy_dir(DIR_CSS, DIR_OUT . '/css');
    copy_dir(DIR_IMGS, DIR_OUT . '/imgs');

    $input_files = get_files(DIR_HTML);
    $pages = $input_files['pages'];
    $template = build_template($input_files['template'], $pages, $config['pages']);
    foreach ($pages as $page_name => $page_content) {
        $html = replace(array(
            '<!--js-->' => js_link('common') . js_link($page_name),
            '<!--css-->' => css_link('common') . css_link($page_name),
            '<!--page_title-->' => strtoupper($page_name),
            '<!--attributions-->' => isset($input_files['attributions'][$page_name]) ? $input_files['attributions'][$page_name] : '',
            '<!--content-->' => escape($page_content),
        ), $template);
        file_put_contents(DIR_OUT . '/' . $config['pages'][$page_name]['file_name'], $html);
    }
}

function js_link($page_name)
{
    return (file_exists(DIR_OUT . '/js/' . $page_name . '/defer.js') ? '<script defer src="./js/' . $page_name . '/defer.js"></script>' : '');
}

function css_link($page_name)
{
    return (file_exists(DIR_OUT . '/css/' . $page_name . '/common.css') ? '<link rel="stylesheet" href="./css/' . $page_name . '/common.css">' : '') .
        (file_exists(DIR_OUT . '/css/' . $page_name . '/desktop.css') ? '<link rel="stylesheet" media="screen and (min-width: 550px) and (min-device-width: 480px)" href="./css/' . $page_name . '/desktop.css">' : '') .
        (file_exists(DIR_OUT . '/css/' . $page_name . '/mobile.css') ? '<link rel="stylesheet" media="screen and (max-width: 550px), (max-device-width: 480px)" href="./css/' . $page_name . '/mobile.css">' : '');
}

function build_template($tmpl, $pages, $pages_config)
{
    $menu = '';
    foreach ($pages as $page => $page_content) {
        $menu .= '<li class="menu-el"><a href="./' . $pages_config[$page]['file_name'] . '">' . strtoupper($pages_config[$page]['page_name']) . '</a></li>';
    }
    return replace(array('<!--menu-->' => $menu), $tmpl);
}

function escape($s)
{
    return replace(array(
        "À" => "&Agrave;",
        "Á" => "&Aacute;",
        "Â" => "&Acirc;",
        "Ã" => "&Atilde;",
        "Ä" => "&Auml;",
        "Å" => "&Aring;",
        "Æ" => "&AElig;",
        "Ç" => "&Ccedil;",
        "È" => "&Egrave;",
        "É" => "&Eacute;",
        "Ê" => "&Ecirc;",
        "Ë" => "&Euml;",
        "Ì" => "&Igrave;",
        "Í" => "&Iacute;",
        "Î" => "&Icirc;",
        "Ï" => "&Iuml;",
        "Ð" => "&ETH;",
        "Ñ" => "&Ntilde;",
        "Ò" => "&Ograve;",
        "Ó" => "&Oacute;",
        "Ô" => "&Ocirc;",
        "Õ" => "&Otilde;",
        "Ö" => "&Ouml;",
        "Ø" => "&Oslash;",
        "Ù" => "&Ugrave;",
        "Ú" => "&Uacute;",
        "Û" => "&Ucirc;",
        "Ü" => "&Uuml;",
        "Ý" => "&Yacute;",
        "Þ" => "&THORN;",
        "ß" => "&szlig;",
        "à" => "&agrave;",
        "á" => "&aacute;",
        "â" => "&acirc;",
        "ã" => "&atilde;",
        "ä" => "&auml;",
        "å" => "&aring;",
        "æ" => "&aelig;",
        "ç" => "&ccedil;",
        "è" => "&egrave;",
        "é" => "&eacute;",
        "ê" => "&ecirc;",
        "ë" => "&euml;",
        "ì" => "&igrave;",
        "í" => "&iacute;",
        "î" => "&icirc;",
        "ï" => "&iuml;",
        "ð" => "&eth;",
        "ñ" => "&ntilde;",
        "ò" => "&ograve;",
        "ó" => "&oacute;",
        "ô" => "&ocirc;",
        "õ" => "&otilde;",
        "ö" => "&ouml;",
        "ø" => "&oslash;",
        "ù" => "&ugrave;",
        "ú" => "&uacute;",
        "û" => "&ucirc;",
        "ü" => "&uuml;",
        "ý" => "&yacute;",
        "þ" => "&thorn;",
        "ÿ" => "&yuml;",
        "'" => "&#39;",
        "’" => "&#39;"
    ), $s);
}

function replace($map, $s)
{
    $replaced = $s;
    foreach ($map as $k => $v) {
        $replaced = str_replace($k, $v, $replaced);
    }
    return $replaced;
}

//UTILS
function get_files($dir, $remove_ext = true)
{
    $files = array();
    explore_dir($dir,
        function ($file, $path) use (&$files) {
            $files[$file] = get_files($path);
        },
        function ($file, $path) use (&$files, $remove_ext) {
            $file_name = $remove_ext ? substr($file, 0, strrpos($file, ".")) : $file;
            $files[$file_name] = file_get_contents($path);
        });
    return $files;
}

function copy_dir($dir_from, $dir_to)
{
    mkdir($dir_to, 0777, true);
    explore_dir($dir_from,
        function ($file, $path) use ($dir_to) {
            copy_dir($path, $dir_to . '/' . $file);
        },
        function ($file, $path) use ($dir_to) {
            $path_to = $dir_to . '/' . $file;
            if (!copy($path, $path_to)) {
                echo 'Errore copiando "' . $path . '" in "' . $path_to . "\"\n";
            }
        });
}

function delete_dir($dir)
{
    explore_dir($dir,
        function ($file, $path) {
            delete_dir($path);
        },
        function ($file, $path) {
            unlink($path);
        });
    rmdir($dir);
}

function explore_dir($dir, $dir_function, $file_function)
{
    foreach (file_list($dir) as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            $dir_function($file, $path);
        } else {
            $file_function($file, $path);
        }
    }
}

function file_list($dir)
{
    return array_diff(scandir($dir), array('..', '.'));
}

// End of File
