<?php
namespace LPO;

class Object {

	/**
	 * @var mixed
	 */
	protected $_id;

	/**
	 * @param mixed $id
	 */
	function __construct($id)
	{
		$this->_id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->_id;
	}
}