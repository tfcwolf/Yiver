<?php
namespace cms\Http;
 /**
 * url编码封装
 *
 * @package MVC
 * @author tfc <yangyi.cn.gz@gmail.com>
 */
class Url {

	public static  function create($uri) {
		return \Config::get('host').$uri;
	}
}