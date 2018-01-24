<?php

define('API_BASE', 'http://guestbook/api/');

$user = [
	'name' => "User",
	'email' => "user@test.com",
	'password' => "123123",
	'password_confirmation' => "123123",
];

$admin = [
	'name' => "Admin",
	'email' => "admin@test.com",
	'is_admin' => true,
	'password' => "123123",
	'password_confirmation' => "123123",
];

//api::register($user);
//api::register($admin);

$content = api::login([
	'email' => 'admin@test.com',
	'password' => '123123',
]);
api::$token = $content->data->api_token;

for ($i = 0; $i < 0; $i ++) {
	$message = [
		'message' => fakeMessage(),
	];
	api::messages($message);
}

api::call('messages/50/answer', ['message' => fakeMessage()]);

//api::call('messages/page/1/25');

//api::logout();
//api::$token = null;

exit;

// --------------------------------------------------------------------------------

class api {

	static $token = null;

	static function __callStatic($name, $args) {
		return self::call($name, $args[0] ?? null);
	}

	static function call($url, $post = null) {
		$options = [
			'httpheader' => [
				'X-Requested-With: XMLHttpRequest'
			],
		];
		if (self::$token) {
//			$url .= '?api_token=' . self::$token;
			$options['httpheader'][] = 'Authorization: Bearer ' . self::$token;
		}
		if ($post) {
			$options['post'] = $post;
		}
		$result = json_decode(curl(API_BASE . $url, $options));
		trace($result);
		return $result;
	}

}

function curl($url, $options = []) {

	global $token;

	trace($url);

	$curl = curl_init(); 

	curl_setopt($curl, CURLOPT_URL, $url);
//	curl_setopt($curl, CURLOPT_VERBOSE, false);
//	curl_setopt($curl, CURLOPT_HEADER, true); 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
	
//	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
//	curl_setopt($curl, CURLOPT_ENCODING, "");
//	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:40.0) Gecko/20100101 Firefox/40.0");
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl, CURLOPT_TIMEOUT, 2);
//	curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
	
	if (isset($options['post'])) {
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $options['post']);
		unset($options['post']);
	}

	foreach ($options as $key => $value)
		if (defined($name = strtoupper('curlopt_' . $key)))
			curl_setopt($curl, constant($name), $value);

//	curl_setopt($curl, CURLINFO_HEADER_OUT, true); // enable tracking

	$content = curl_exec($curl);
	
//	$headerSent = curl_getinfo($curl, CURLINFO_HEADER_OUT);
//	trace($headerSent);

	$error = curl_errno($curl) ? "(#" . curl_errno($curl) . ") " . curl_error($curl) : "";
	$header = curl_getinfo($curl);

	curl_close($curl);
	
	if ($error)
		trace($error);

	return $content;
}

function trace($data) {
	print_r('<pre>');
	print_r($data);
	print_r('</pre>');
}

function fakeMessage() {

	static $fakeData = null;

	if (!$fakeData) {
		$fakeData = explode('. ', "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus nec mi pellentesque, molestie sapien eu, posuere felis. Pellentesque consequat turpis non nisl pulvinar laoreet. Nunc eu justo et odio consequat faucibus. Vivamus tincidunt elit a sollicitudin vulputate. Nullam egestas lorem dolor, sed feugiat odio fringilla a. Proin gravida cursus urna, sit amet pharetra risus viverra vitae. Vestibulum facilisis urna sed nulla pellentesque, eu egestas justo vestibulum. Duis vitae viverra lectus, in dictum metus. Nunc dictum pulvinar dapibus. Vestibulum sed laoreet ex. Sed gravida, velit ac pretium posuere, purus risus lacinia nulla, nec luctus enim risus eu magna. Nulla ac fringilla risus. Maecenas tempus volutpat nulla, vel fringilla mauris dignissim et. Maecenas sit amet arcu sem. Nunc nisi massa, finibus et placerat a, interdum a mi. Nullam vestibulum elementum sodales. Aenean tincidunt lectus ultrices libero bibendum egestas. Nullam sodales tincidunt facilisis. Pellentesque a euismod magna. Sed libero augue, fringilla nec venenatis vitae, convallis non purus. Duis lacinia eros vitae accumsan malesuada. Mauris ut ultricies urna. Cras eu ultrices nibh. Mauris sed odio erat. Aliquam ornare massa leo, eu luctus turpis sodales sed. Integer iaculis tempus dolor, mollis ultrices tellus consectetur at. Morbi quis nunc auctor, bibendum eros a, malesuada tortor. Pellentesque in tellus risus. Proin tincidunt tincidunt augue, quis porttitor dui rutrum at. Nam sed condimentum massa, sed facilisis sapien. Fusce vitae mi dapibus, semper tortor imperdiet, egestas libero. Nullam posuere ultrices urna a aliquam. Nam gravida luctus ultrices. Etiam ac dolor neque. Etiam dictum cursus ligula vel fermentum. Sed quam mi, porttitor eget mi at, faucibus interdum massa. Maecenas pulvinar diam ut tortor dictum, sit amet dictum enim suscipit. Integer in laoreet augue. Nulla vitae egestas lacus, a condimentum justo. In pharetra velit eros, non congue diam facilisis ac. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Suspendisse ultricies erat ac lorem vestibulum, non sodales diam euismod. Vestibulum tempus euismod dictum. Ut ut mattis lorem, vel consectetur sapien. Maecenas vitae faucibus elit, nec viverra risus. Nunc mi orci, pellentesque ac vestibulum fringilla, sodales ut nisl. Sed metus eros, fermentum ac vehicula id, vestibulum et nunc. Proin non ligula mi. Quisque facilisis felis nisi, quis eleifend dolor porta id. Nulla vitae metus lacinia, volutpat tellus a, mattis est. Nam at sodales enim. Morbi quis malesuada diam. In hac habitasse platea dictumst. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas molestie diam mauris, a bibendum magna mollis eu. Phasellus viverra turpis ut nisi cursus tincidunt. Sed arcu ex, bibendum id viverra et, dignissim eget sapien");
	}

	$count = rand(1, 3);
	$message = [];
	for ($i = 0; $i < $count; $i ++) {
		$message[] = $fakeData[rand(0, count($fakeData) - 1)];
	}

	return implode('. ', $message) . '.';
}

?>
