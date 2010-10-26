<?php

namespace Ergo\Logging;

/**
 * A basic implementation of a composite logger that multiplexes log messages to many loggers
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class LoggerMultiplexer extends AbstractLogger implements CompositeLogger
{
	private $_loggers=array();

	/**
	 * Constructor
	 */
	function __construct($loggers=array(), $level=\Ergo\Logger::INFO)
	{
		parent::__construct($level);
		$this->addLoggers($loggers);
	}

	/* (non-phpdoc)
	 * @see CompositeLogger::addLoggers()
	 */
	function addLoggers($loggers)
	{
		foreach(func_get_args() as $logger)
		{
			if(is_array($logger))
			{
				foreach($logger as $sublogger)
				{
					$this->addLoggers($sublogger);
				}
			}
			else if(is_object($logger))
			{
				$this->_loggers[] = $logger;
			}
		}

		return $this;
	}

	/* (non-phpdoc)
	 * @see CompositeLogger::clearLoggers()
	 */
	function clearLoggers()
	{
		$this->_loggers = array();
		return $this;
	}

	/* (non-phpdoc)
	 * @see \Ergo\Logger::log()
	 */
	function log($message,$level=\Ergo\Logger::INFO)
	{
		foreach($this->_loggers as $logger)
			$logger->log($message, $level);

		return $this;
	}

	/* (non-phpdoc)
	 * @see \Ergo\Logger::logException()
	 */
	function logException($exception,$level=\Ergo\Logger::ERROR)
	{
		foreach($this->_loggers as $logger)
			$logger->logException($exception,$level);

		return $this;
	}

	/* (non-phpdoc)
	 * @see \Ergo\Logger::setLogLevel()
	 */
	function setLogLevel($level)
	{
		parent::setLogLevel($level);

		foreach($this->_loggers as &$logger)
		{
			$logger->setLogLevel($level);
		}
	}

	/**
	 * Calls any method on loggers
	 */
	public function __call($method, $params)
	{
		foreach($this->_loggers as $logger)
			call_user_func_array(array($logger, $method), $params);

		return $this;
	}
}
