<?php

namespace Ergo\Http;

\Mock::generate('Ergo\Http\Transport', 'MockTransport');

class ClientTest extends \UnitTestCase
{
	public function transport()
	{
		$transport = new \MockTransport();
		return Client::transport($transport);
	}

	public function client()
	{
		return new Client('http://example.org');
	}

	public function testRequestMethodsDelegateToTransport()
	{
		$methods = array('GET', 'POST', 'PUT', 'DELETE');
		foreach ($methods as $method)
		{
			$transport = $this->transport();
			$transport->expectOnce(
				'send',
				array(new \IsAExpectation('\Ergo\Http\Request')),
				"Expect $method to delegate to send with Request: %s"
			);
			$transport->setReturnValue(
				'send', new Response(200, array())
			);
			$client = new Client('http://example.org');
			$this->client()->$method('/hello', 'content');
		}
	}

	public function testInternalServerErrorStatusCodeThrowsException()
	{
		$this->transport()->setReturnValue('send', new Response(500, array()));

		$this->expectException('Ergo\Http\Error');

		$this->client()->get('/hello');
	}

	public function testSetTimeoutDelegatesToTransport()
	{
		$this->transport()->expectOnce('setTimeout', array(4));

		$this->client()->setTimeout(4);
	}

	public function testSetHttpProxyDelegatesToTransport()
	{
		$this->transport()->expectOnce('setHttpProxy', array('http://my.proxy.url'));

		$this->client()->setHttpProxy('http://my.proxy.url');
	}

	public function testSetHttpAuthDelegatesToTransport()
	{
		$this->transport()->expectOnce('setHttpAuth', array('username', 'password'));

		$this->client()->setHttpAuth('username', 'password');
	}
}