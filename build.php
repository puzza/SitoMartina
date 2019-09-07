<?php

const DIR_OUT = 'compiled';
const DIR_IN = 'implementation';
const DIR_HTML = DIR_IN . '/html';
const DIR_CSS = DIR_IN . '/css';
const DIR_IMGS = DIR_IN . '/imgs';

main();

function main()
{
    delete_dir(DIR_OUT);
    copy_dir(DIR_CSS, DIR_OUT . '/css');
    copy_dir(DIR_IMGS, DIR_OUT . '/imgs');

    $input_files = get_files(DIR_HTML);
    $pages = $input_files['pages'];
    $template = build_template($input_files['template'], $pages);
    foreach ($pages as $page_name => $page_content) {
        $html = replace(array(
            '<!--css-->' => css_link('common') . css_link($page_name),
            '<!--page_title-->' => strtoupper($page_name),
            '<!--content-->' => $page_content,
        ), $template);
        file_put_contents(DIR_OUT . '/' . $page_name . '.html', $html);
    }
}

function css_link($page_name)
{
    return (file_exists(DIR_OUT . '/css/' . $page_name . '/common.css') ? '<link rel="stylesheet" href="./css/' . $page_name . '/common.css">' : '') .
        (file_exists(DIR_OUT . '/css/' . $page_name . '/desktop.css') ? '<link rel="stylesheet" media="screen and (min-width: 500px)" href="./css/' . $page_name . '/desktop.css">' : '') .
        (file_exists(DIR_OUT . '/css/' . $page_name . '/mobile.css') ? '<link rel="stylesheet" media="screen and (max-width: 500px)" href="./css/' . $page_name . '/mobile.css">' : '');
}

function build_template($tmpl, $pages)
{
    $menu = '';
    foreach ($pages as $page_name => $page_content) {
        $menu .= '<li class="menu-el"><a href="./' . $page_name . '.html">' . strtoupper($page_name) . '</a></li>';
    }
    return replace(array('<!--menu-->' => $menu), $tmpl);
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
