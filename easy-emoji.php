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
function mce_smiley_button($buttons)
{
	array_push($buttons, 'smiley'); // 添加 'smiley' 按钮到 TinyMCE 编辑器中
	return $buttons;
}
add_filter('mce_buttons', 'mce_smiley_button');

// 添加表情插件
function mce_smiley_js($plugin_array)
{
	$plugin_array['smiley'] = plugins_url('/plugin.js', __FILE__); // 指定表情插件的 js 文件路径
	return $plugin_array;
}
add_filter('mce_external_plugins', 'mce_smiley_js');

// 添加表情样式
function mce_smiley_css()
{
	wp_enqueue_style('tinymce-smiley-button', plugins_url('/plugin.css', __FILE__)); // 加载表情插件的 css 样式
}
add_action('admin_enqueue_scripts', 'mce_smiley_css');
add_action('wp_enqueue_scripts', 'mce_smiley_css');


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

function mce_smiley_settings($settings)
{
	if (get_option('use_smilies')) { // 如果启用了表情
		$smilies = getImageJson();

		echo '<script>window.easy_emoji_json_data = ' . json_encode($smilies) . '</script>'; // 将表情设置以 JavaScript 对象的形式传递给客户端
	}
	return $settings;
}

add_filter('tiny_mce_before_init', 'mce_smiley_settings');
