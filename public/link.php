<?php
/**
 * Created by PhpStorm.
 * User: mydn
 * Date: 2018/8/14
 * Time: 下午4:42
 */
//$link = $_POST['link'];
ini_set('date.timezone','Asia/Shanghai');

$today = date('Y-m-d H:i:s');

require '../model/model.php';

$act = isset($_POST['act']) ? $_POST['act'] : '';

switch ($act)
{
	case 'create':
		$res = create();
		echo json_encode($res);die;
		break;
	default:
//		$Model = new Model('yz_links');
		header("Location: /");
		break;
}



function create()
{
	$res = array('code'=>201,'msg'=>'');
	$postdata = $_POST;

	$url = isset($postdata['link']) ? $postdata['link'] : '';

	if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL))
	{
		$res['msg'] = '格式错误';
		return $res;
	}
	// 追加http://
	$server_name = $_SERVER['HTTP_HOST'];
	$hostname = getHttpLink($server_name);

	$url = getHttpLink($url);

	$Model = new Model('yz_links');
	$info = $Model->doFind(array('url'=>$url));

	// 该链接存在，则返回结果，否则增加
	if ($info && $info['keyword']) {
		$res['code'] = 200;
		$res['data'] = array('short_link'=>$hostname.'/'.$info['keyword']);
		return $res;

	} else {
		$urlArr = shortUrl($url);
		$keyword = $urlArr[1];
		$arr = array(
			'url' => $url,
			'keyword' => $keyword,
			'type_link' => 'system',
			'create_time' => date('Y-m-d H:i:s'),
			'update_time' => date('Y-m-d H:i:s'),
		);

		if ($info && $info[0] && empty($info[0]['keyword'])) {
			$res_data = $Model->doFind(array('url'=>$url), array('keyword'=>$keyword));

		} else {
			$res_data = $Model->doAdd($arr);
		}

		if ($res_data) {

			$res['code'] = 200;
			$res['msg'] = '成功';
			$res['data'] = array('short_link'=>$hostname.'/'.$keyword);
			return $res;

		} else {
			$res['msg'] = '失败';
			return $res;
		}
	}

}

//$a = getRand('http://www.baidu.com');
//var_dump($a);

function getHttpLink($url)
{
	if (!preg_match("/^(http|ftp):/", $url)) {
		$url = 'http://'.$url;
	}
	return $url;
}

function shortUrl($input)
{
	$key = 'yz2008';
	$base32 = array (
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
		'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
		'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
		'y', 'z', '0', '1', '2', '3', '4', '5'
	);

	$hex = md5($key.$input);
	$hexLen = strlen($hex);
	$subHexLen = $hexLen / 8;
	$output = array();
echo '<pre>';
	for ($i = 0; $i < $subHexLen; $i++) {
		//把加密字符按照8位一组16进制与0x3FFFFFFF(30位1)进行位与运算
		$subHex = substr ($hex, $i * 8, 8);

		$int = 0x3FFFFFFF & (1 * ('0x'.$subHex));
		$out = '';

		for ($j = 0; $j < 8; $j++) {

			//把得到的值与0x0000001F进行位与运算，取得字符数组chars索引
			$val = 0x0000001F & $int;
			$out .= getRand($val);
			$int = $int >> 5;
		}

		$output[] = $out;
	}

	return $output;
}

function getRand($k)
{
	$base32_1 = array (
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
		'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
		'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
		'y', 'z', '0', '1', '2', '3', '4', '5'
	);
	$base32_2 = array (
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
		'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
		'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
		'Y', 'Z', '5', '4', '3', '2', '1', '0'
	);

	$rand_val = mt_rand(1,2);
	if ($rand_val === 2) {
		$v = $base32_1[$k];
	} else {
		$v = $base32_2[$k];
	}

	return $v;
}