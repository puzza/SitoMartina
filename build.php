<?php

//require __DIR__ . "/../scssphp/scss.inc.php";

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
    $icons_replace = get_icons_replace($config);

    delete_dir(DIR_OUT);
    copy_dir(DIR_JS, DIR_OUT . '/js');
    //copy_dir(DIR_CSS, DIR_OUT . '/css');
    copy_dir(DIR_IMGS, DIR_OUT . '/imgs');

    copy_css_files(get_files(DIR_CSS), DIR_OUT . '/css');

    $input_files = get_files(DIR_HTML);
    $pages = $input_files['pages'];
    foreach ($pages as $page_name => $page_content) {
        $html = replace($icons_replace, replace(array(
            '<!--menu-->' => build_menu($page_name, $config),
            '<!--js-->' => js_link('common') . js_link($page_name),
            '<!--css-->' => css_link('common') . css_link($page_name),
            '<!--page_title-->' => strtoupper($page_name),
            //'<!--attributions-->' => isset($input_files['attributions'][$page_name]) ? $input_files['attributions'][$page_name] : '',
             '<!--content-->' => escape($page_content),
        ), $input_files['template']));
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
        (file_exists(DIR_OUT . '/css/' . $page_name . '/rsp-desktop.css') ? '<link rel="stylesheet" media="screen and (min-width: 550px) and (min-device-width: 480px)" href="./css/' . $page_name . '/rsp-desktop.css">' : '') .
        (file_exists(DIR_OUT . '/css/' . $page_name . '/rsp-mobile.css') ? '<link rel="stylesheet" media="screen and (max-width: 550px), (max-device-width: 480px)" href="./css/' . $page_name . '/rsp-mobile.css">' : '');
}

function build_menu($current_page, $config)
{
    $pages_config = $config['pages'];
    $menu = '';
    foreach ($config['menu']['ordered_pages'] as $page) {
        $current_class = $page == $current_page ? ' selected' : '';
        $menu .= '<li class="menu-el' . $current_class . '"><a href="./' . $pages_config[$page]['file_name'] . '">' . strtoupper($pages_config[$page]['page_name']) . '</a></li>';
    }
    return $menu;
}

function get_icons_replace($config)
{
    $icons_replace = array();
    foreach ($config['icons'] as $name => $icon) {
        $icons_replace['<!--icon-' . $name . '-->'] = svg($icon);
    }
    return $icons_replace;
}

function svg($data)
{
    return '<svg class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="' . $data['vb'] . '"><path d="' . $data['d'] . '"/></svg>';
}

function copy_css_files($dirs, $dir_out)
{
    foreach ($dirs as $page_name => $files) {
        $dir_to = $dir_out . '/' . $page_name;
        mkdir($dir_to, 0777, true);
        $common_css = '';
        foreach ($files as $file_name => $css) {
            if ($file_name == 'rsp-desktop' || $file_name == 'rsp-mobile') {
                file_put_contents($dir_to . '/' . $file_name . '.css', $css);
            } else {
                $common_css .= $css . "\n";
            }
        }
        file_put_contents($dir_to . '/common.css', $common_css);
    }
}

//UTILS
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
        "'" => "&rsquo;", //"&#39;",
         "’" => "&rsquo;",
        '”' => "&rdquo;",
        '“' => "&ldquo;",
        '–' => '&ndash;',
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

function compile_css_dir($dir_from, $dir_to)
{
    mkdir($dir_to, 0777, true);
    explore_dir($dir_from,
        function ($file, $path) use ($dir_to) {
            compile_css_dir($path, $dir_to . '/' . $file);
        },
        function ($file, $path) use ($dir_to) {
            $scss = new scssc();
            $path_to = $dir_to . '/' . $file;
            file_put_contents($path_to, $scss->compile(file_get_contents($path)));
        }
    );
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
