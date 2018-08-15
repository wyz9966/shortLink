<?php
/**
 * Created by PhpStorm.
 * User: mydn
 * Date: 2018/8/15
 * Time: 上午9:58
 */
$notFind = 'http://'. $_SERVER['HTTP_HOST'] .'/notfind.html';
$createlink = 'http://'. $_SERVER['HTTP_HOST'] .'/index.html';

$request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

$arr = explode('/', $request_uri);

$keyword = isset($arr[1]) ? $arr[1] : '';

if ($keyword)
{
	if (!in_array(count($keyword), array(8)))
	{
		header("Location: ".$notFind);
	}

	require '../model/model.php';

	$Model = new Model('yz_links');

	$info = $Model->doFind(array('keyword'=>$keyword));

	if ($info && isset($info['url'])) {
		header("Location: ".$info['url']);
	} else {

		header("Location: ".$notFind);
	}

} else {
	header("Location: ".$createlink);
}




