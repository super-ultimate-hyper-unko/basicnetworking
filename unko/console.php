<?php
namespace unko;

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'autoload.php');

$host = isset($argv[1]) ? $argv[1] : null;
$port = isset($argv[2]) ? $argv[2] : null;

if(!$host || !$port)
{
	exit('requrie host or address and port' . PHP_EOL);
}

$socket = new TcpSocket();
$socket->open();
$socket->connect($host, $port);
$socket->write("GET / HTTP/1.1\r\nHost: example.com\r\n\r\n");
while(($response = $socket->read(2000)))
{
	echo PHP_EOL. "[" . strlen($response) . "]" . PHP_EOL;
	echo $response;
	echo "まだ読めますか？";
	if($socket->isReadable())
	{
		echo "読めま～す" . PHP_EOL;
	}
	else
	{
		echo "読めませ～ん" . PHP_EOL;
		exit();
	}
}
