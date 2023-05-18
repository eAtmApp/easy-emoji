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
add_filter('mce_buttons', 'mce_button');



// 添加js代码
function mce_javascript($plugin_array)
{
	$plugin_array['easy_emoji'] = plugins_url('/plugin.js', __FILE__); // 指定表情插件的 js 文件路径
	return $plugin_array;
}
add_filter('mce_external_plugins', 'mce_javascript');

// 添加css样式
function mce_styles()
{
	wp_enqueue_style('tinymce-easy_emoji-button', plugins_url('/plugin.css', __FILE__)); // 加载表情插件的 css 样式
}
add_action('admin_enqueue_scripts', 'mce_styles');
add_action('wp_enqueue_scripts', 'mce_styles');

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

	//return json_encode($result, JSON_PRETTY_PRINT);
	return $result;
}

function mce_html_init($settings)
{
	$smilies = getImageJson();
	echo '<script>window.easy_emoji_json_data = ' . json_encode($smilies) . '</script>'; // 将表情设置以 JavaScript 对象的形式传递给客户端

	return $settings;
}

add_filter('tiny_mce_before_init', 'mce_html_init');


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
