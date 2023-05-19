<?php

/**
 * Plugin Name: easy emoji
 * Description: 让TinyMCE支持添加表情图片.
 * Plugin URI: http://wordpress.org/extend/plugins/easy-emoji/
 * Version: 1.0.1
 * Author: SilenceRet
 * Author URI: https://eatm.app/
 */

// 添加表情按钮
function mce_button($buttons)
{
	array_push($buttons, 'easy_emoji'); // 添加 'smiley' 按钮到 TinyMCE 编辑器中
	return $buttons;
}
//add_filter('mce_buttons', 'mce_button',999);	//mce_buttons会被Elementor清除掉,所以用下面第2行工具栏
add_filter('mce_buttons_2', 'mce_button', 999);	//第2行工具栏

/* 
//不用这个添加css了,Elementor没进入
function easy_style()
{
	wp_enqueue_style('easy-emoji-style', plugins_url('/plugin.css', __FILE__)); // 加载表情插件的 css 样式
}
add_action('init', "easy_style"); */
//add_action('admin_enqueue_scripts', 'easy_style');
//add_action('wp_enqueue_scripts', 'easy_style');


// 添加css样式
/*

function easy_script()
{
	wp_enqueue_script('easy-emoji-script', plugins_url('/plugin.js', __FILE__), array(), '1.0.0.1', true);
}
*/

//用作测试.
//add_action('admin_enqueue_scripts', 'easy_script');
//add_action('wp_enqueue_scripts', 'easy_script'); 



function getImageJson()
{
	$dirs = glob(plugin_dir_path(__FILE__) . 'image/*/', GLOB_ONLYDIR);

	$result = [];
	foreach ($dirs as $dir) {
		if (is_file($dir)) {
			continue;
		}

		$key = basename($dir);
		$result[$key] = [
			'names' => [],
			'url' => plugin_dir_url(__FILE__) . "image/" . $key  . "/"
		];

		$files = glob("$dir/*");
		foreach ($files as $file) {
			if (is_dir($file)) {
				continue;
			}
			$filename = basename($file);
			$result[$key]['names'][] = $filename;
		}
	}
	return $result;
}

/* 
//这个方式注入不进Elementor
function mce_html_init($settings)
{
	$smilies = getImageJson();
	echo '<script>window.easy_emoji_json_data = ' . json_encode($smilies) . '</script>'; // 将表情设置以 JavaScript 对象的形式传递给客户端
	return $settings;
}
add_filter('tiny_mce_before_init', 'mce_html_init'); */


//让前台的TinyMCE加载时,能加载自己的js代码
function mce_javascript($plugin_array)
{
	//主脚本
	$plugin_array['easy_emoji'] = plugins_url('/plugin.js', __FILE__); // 指定表情插件的 js 文件路径

	//数据文件
	$smilies = getImageJson();
	$dataStr='window.easy_emoji_json_data = ' . json_encode($smilies);
	file_put_contents(plugin_dir_path(__FILE__).'/data.js', $dataStr);
	$plugin_array['easy_emoji_data'] = plugins_url('/data.js', __FILE__);

	return $plugin_array;
}
add_filter('mce_external_plugins', 'mce_javascript', 999, 1);





function easy_emoji_clock_assets()
{
	$dependencies = array('react', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-polyfill', 'wp-rich-text');
	$version = "v1.0.0.1";

	// Enqueue scripts.
	wp_enqueue_script(
		'easy-emoji-block-script',
		plugins_url('/plugin.js', __FILE__),
		$dependencies,
		$version,
		true
	);

	// Load script translations: https://developer.wordpress.org/reference/functions/wp_set_script_translations/
	//wp_set_script_translations('easy-emoji-block-script', 'emoji-toolbar', plugin_dir_path(__FILE__) . '/languages/');

	// Enqueue styles.
	wp_enqueue_style(
		'easy-emoji-block-script-style',
		plugins_url('/plugin.css', __FILE__),
		array(),
		$version
	);

	wp_localize_script('easy-emoji-json-data', 'my_data', array(
		'foo' => 'bar',
		'baz' => 'qux'
	));
}

add_action('enqueue_block_editor_assets', 'easy_emoji_clock_assets');
