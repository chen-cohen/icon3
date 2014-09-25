<?php


namespace LPO\Db\Redis;

class ProtocolGenerator {


	public $protocol_str = '';

	function __call($name, $arguments)
	{
		if(method_exists($this,$name))
		{
			return call_user_func_array($this->$name,$arguments);
		}

		$argc = 1;
		$name = strtoupper($name);
		$cmdLength = mb_strlen($name);
		$cmd = "$$cmdLength\r\n$name\r\n";

		foreach($arguments as $argv)
		{
			if(is_array($argv))
			{
				if(count($argv) < 1)
				{
					return false;
				}

				foreach($argv as $key=>$value)
				{
					$argc += 2;
					$cmd .= $this->_getArg($key);
					$cmd .= $this->_getArg($value);
				}
			}
			else
			{
				$argc++;
				$cmd .= $this->_getArg($argv);
			}
		}
		return $this->protocol_str .= "*$argc\r\n".$cmd;
	}

	/**
	 * @return string
	 */
	public function getProtocolStr()
	{
		return $this->protocol_str;
	}

	/**
	 * @param $argv
	 *
	 * @return string
	 */
	protected function _getArg($argv)
	{
		$cmdLength = mb_strlen($argv);
		return "$$cmdLength\r\n$argv\r\n";
	}
}