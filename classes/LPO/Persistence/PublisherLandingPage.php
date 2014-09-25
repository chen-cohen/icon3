<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;

class PublisherLandingPage extends CRUD {

	/**
	 * @var string
	 */
	const TABLE_NAME = 'lpo_db_ORM.publisher_landing_page';
	/**
	 * @var int
	 */
	protected $_landing_page_id;
	/**
	 * @var int
	 */
	protected $_publisher_id;

	/**
	 * @param array $data
	 */
	protected function __construct(array $data)
	{
		$this->_landing_page_id = (int)$data['landing_page_id'];
		$this->_publisher_id    = (int)$data['publisher_id'];

	}

	/**
	 * @return mixed
	 */
	public function create()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('INSERT INTO '.static::TABLE_NAME.' SET `landing_page_id` = :landing_page_id, `publisher_id` = :publisher_id');

		$stmt->bindValue(':landing_page_id', $this->_landing_page_id);
		$stmt->bindValue(':publisher_id', $this->_publisher_id);

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
		// TODO: Implement read() method.
	}

	/**
	 * @return mixed
	 */
	public function delete()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('DELETE FROM '.static::TABLE_NAME.' WHERE `landing_page_id` = :landing_page_id AND `publisher_id` = :publisher_id');

		$stmt->bindValue(':landing_page_id', $this->_landing_page_id,$adapter::PARAM_INT);
		$stmt->bindValue(':publisher_id', $this->_publisher_id,$adapter::PARAM_INT);

		return $stmt->execute();
	}

	/**
	 * @param int $landingPageId
	 *
	 * @return bool
	 */
	public static function deleteAllByLandingPageId($landingPageId)
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('DELETE FROM '.static::TABLE_NAME.' WHERE `landing_page_id` = :landing_page_id');
		$stmt->bindValue(':landing_page_id', (int)$landingPageId, $adapter::PARAM_INT);
		return $stmt->execute();
	}
	/**
	 * @param int $publisherId
	 *
	 * @return bool
	 */
	public static function deleteAllByPublisherId($publisherId)
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('DELETE FROM '.static::TABLE_NAME.' WHERE `publisher_id` = :publisher_id');
		$stmt->bindValue(':publisher_id', (int)$publisherId, $adapter::PARAM_INT);
		return $stmt->execute();
	}

	/**
	 * @param int $landing_page_id
	 */
	public function setLandingPageId($landing_page_id)
	{
		$this->_landing_page_id = $landing_page_id;
	}

	/**
	 * @return int
	 */
	public function getLandingPageId()
	{
		return $this->_landing_page_id;
	}

	/**
	 * @param int $publisher_id
	 */
	public function setPublisherId($publisher_id)
	{
		$this->_publisher_id = $publisher_id;
	}

	/**
	 * @return int
	 */
	public function getPublisherId()
	{
		return $this->_publisher_id;
	}

}