<?php
namespace unko;

abstract class Socket
{
	const READ_BINARY = PHP_BINARY_READ;
	const READ_TEXTLINE = PHP_NORMAL_READ;

	protected $socket;
	protected $error;

	public function __construct()
	{
		$this->socket = null;
	}

	public function open()
	{
		throw $this->getException("Not Implementd open()");
	}

	public function connect($address, $port)
	{
		$this->checkSocket();

		socket_clear_error($this->socket);
		$result = socket_connect($this->socket, $address, $port);
		if($result)
		{
			return;
		}
		throw $this->getException();
	}

	public function close()
	{
		if(!$this->socket)
		{
			return;
		}

		socket_clear_error($this->socket);
		socket_close($this->socket);
		$this->socket = null;
	}

	public function write($data, $length = null)
	{
		$this->checkSocket();

		if($length === null)
		{
			$length = strlen($data);
		}
		socket_clear_error($this->socket);
		$size = socket_write($this->socket, $data, $length);
		if($size === false)
		{
			throw $this->getException();
		}
		return $size;
	}

	public function read($length, $type = null)
	{
		$this->checkSocket();

		if($type === null)
		{
			$type = PHP_BINARY_READ;
		}
		socket_clear_error($this->socket);
		return socket_read($this->socket, $length, $type);
	}

	public function isReadable()
	{
		$this->checkSocket();

		$read = array($this->socket);
		$write = null;
		$except = null;
		$count = socket_select($read, $write, $except, 0);
		if(0 < $count)
		{
			return (0 < count($read));
		}
		return false;
	}

	public function setTimeout($second, $type = null)
	{
		$this->checkSocket();

		$value = [];
		$value['sec'] = $second;
		$value['usec'] = 0;

		if($type)
		{
			$result = socket_set_option($this->socket, SOL_SOCKET, $type, $value);
			return ($result ? 1 : 0);
		}
		else
		{
			$v = 0;
			$result = socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, $value);
			$v = ($result ? 1 : 0);
			$result = socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, $value);
			$v = $v + ($result ? 1 : 0);
			return $v;
		}
	}

	public function setReceiveTimeout($second)
	{
		return $this->setTimeout($second, SO_RCVTIMEO);
	}

	public function setSendTimeout($second)
	{
		return $this->setTimeout($second, SO_SNDTIMEO);
	}

	protected function getException($message = null)
	{
		if($message === null)
		{
			$error = socket_last_error($this->socket);
			$message = socket_strerror($error);
		}
		$e = new SocketException($message);
		return $e;
	}

	protected function checkSocket()
	{
		if(!$this->socket)
		{
			throw $this->getException("the socket is not open");
		}
	}
}
