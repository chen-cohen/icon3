<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;

class Brand extends CRUD {

	/**
	 * @var string
	 */
	const TABLE_NAME = 'lpo_db_ORM.brand';
	/**
	 * @var string
	 */
	protected $_id;
	/**
	 * @var string
	 */
	protected $_links;
	/**
	 * @var string
	 */
	protected $_name;
	/**
	 * @var string
	 */
	protected $_text;

	/**
	 * @param array $data
	 */
	protected function __construct(array $data)
	{
		$this->_id    = (int)$data['id'];
		$this->_links = (string)$data['links'];
		$this->_name  = (string)$data['name'];
		$this->_text  = (string)$data['text'];
	}

	/**
	 * @return mixed
	 */
	public function create()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('INSERT INTO '.static::TABLE_NAME.' (name,text,links) VALUES (:name,:text,:links)');

		$stmt->bindValue(':name', $this->_name);
		$stmt->bindValue(':text', $this->_text);
		$stmt->bindValue(':links', $this->_links);

		return $stmt->execute();
	}

	/**
	 * @return mixed
	 */
	public function read()
	{
		// TODO: Implement read() method.
	}

	/**
	 * @return mixed
	 */
	public function update()
	{
		// TODO: Implement update() method.
	}

	/**
	 * @return mixed
	 */
	public function delete()
	{
		// TODO: Implement delete() method.
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->_id = $id;
	}

	/**
	 * @return string
	 */
	public function getLinks()
	{
		return $this->_links;
	}

	/**
	 * @param string $links
	 */
	public function setLinks($links)
	{
		$this->_links = $links;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->_name = $name;
	}

	/**
	 * @return string
	 */
	public function getText()
	{
		return $this->_text;
	}

	/**
	 * @param string $text
	 */
	public function setText($text)
	{
		$this->_text = $text;
	}
}