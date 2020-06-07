<?php
namespace unko;

class TcpSocket extends Socket
{
	const TIMEOUT_DEFAULT = 5;
	public function __construct()
	{
		$this->open();
	}

	public function open()
	{
		socket_clear_error();
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if(!$this->socket)
		{
			$error = socket_last_error();
			throw $this->getException(socket_strerror($error));
		}
		$this->setTimeout(self::TIMEOUT_DEFAULT);
	}
}