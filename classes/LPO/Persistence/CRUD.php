<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;

abstract class CRUD {

	const TABLE_NAME = '';

	/**
	 * @param array $data
	 */
	abstract protected function __construct(array $data);

	/**
	 * @param array $data
	 *
	 * @param bool $lazy
	 *
	 * @return static
	 */
	public static function factory($data, $lazy = false)
	{
		return new static($data,$lazy);
	}

	/**
	 * @param string $key
	 * @param string|int|float $value
	 * @param bool $lazy
	 *
	 * @return static
	 */
	public static function loadBy($key, $value,$lazy = false)
	{
		$adapter      = Dbase::getConnection();
		$PDOStatement = $adapter->prepare('SELECT * FROM '.static::TABLE_NAME.' WHERE `'.$key.'` = :value LIMIT 1');

		$PDOStatement->bindValue(':value', $value);
		$PDOStatement->execute();

		$data = $PDOStatement->fetch($adapter::FETCH_ASSOC);
		if(!$data)
		{
			return null;
		}

		return static::factory($data,$lazy);
	}

	/**
	 *
	 * @param bool $lazy
	 *
	 * @return static[]
	 */
	public static function loadAll($lazy = false)
	{
		$adapter = Dbase::getConnection();

		$stmt        = $adapter->query('SELECT * FROM '.static::TABLE_NAME);
		$objectArray = array();
		$dataSet = array();

		while($row = $stmt->fetch($adapter::FETCH_ASSOC))
		{
			$dataSet[] = $row;
		}

		foreach($dataSet as $result)
		{
			$objectArray[] = static::factory($result,$lazy);
		}

		if(empty($objectArray))
		{
			return array();
		}

		return $objectArray;
	}

	/**
	 *
	 * @param string $key
	 * @param array $value
	 * @param bool $lazy
	 *
	 * @return static[]
	 */
	public static function loadMultipleBy($key,array $value,$lazy = false)
	{
		$adapter = Dbase::getConnection();

		$stmt        = $adapter->query('SELECT * FROM '.static::TABLE_NAME.' WHERE '.$key.' IN ('.implode(',',$value).')');
		$objectArray = array();
		$dataSet = array();

		while($row = $stmt->fetch($adapter::FETCH_ASSOC))
		{
			$dataSet[] = $row;
		}

		foreach($dataSet as $result)
		{
			$objectArray[] = static::factory($result,$lazy);
		}

		if(empty($objectArray))
		{
			return null;
		}

		return $objectArray;
	}
	/**
	 *
	 * @param string $column
	 * @param array $values
	 *
	 * @return int
	 */
	public static function deleteMultipleBy($column,array $values)
	{
		return Dbase::getConnection()->exec('DELETE FROM '.static::TABLE_NAME.' WHERE '.$column.' IN ('.implode(',',$values).')');
	}
	/**
	 * @param string $returnedClassType
	 * @param string|int|float $value
	 * @param string $relationIndex
	 *
	 * @return array
	 */
	public function fetchOneToManyRelation($returnedClassType, $value, $relationIndex)
	{
		/**
		 * @var CRUD $returnedClassType
		 */

		$adapter      = Dbase::getConnection();
		$PDOStatement = $adapter->prepare('SELECT tbl.* FROM '.$returnedClassType::TABLE_NAME.' AS `tbl` WHERE tbl.'.$relationIndex.' = :value');

		$PDOStatement->bindValue(':value', $value);

		$PDOStatement->execute();
		$data = $PDOStatement->fetchAll($adapter::FETCH_ASSOC);

		if(!$data)
		{
			return null;
		}

		$collection = array();
		foreach($data as $element)
		{
			$collection[] = $returnedClassType::factory($element);
		}

		return $collection;
	}

	/**
	 * @param string $junctionTableName
	 * @param string $returnedClassType
	 *
	 * @param string $index
	 * @param string|int|float $value
	 * @param string $relationIndex
	 *
	 * @return array
	 */
	public function fetchManyToManyRelation($junctionTableName, $returnedClassType, $index, $value, $relationIndex )
	{
		/**
		 * @var CRUD $returnedClassType
		 */

		$adapter      = Dbase::getConnection();
		$PDOStatement = $adapter->prepare('
		SELECT `main`.* FROM '.$junctionTableName.' AS `link`
		INNER JOIN '.$returnedClassType::TABLE_NAME.' AS `main` ON `main`.id = `link`.'.$relationIndex.'
		WHERE `link`.'.$index.' = :value');

		$PDOStatement->bindValue(':value', $value,$adapter::PARAM_INT);
		$PDOStatement->execute();

		$dataSet = array();

		while($row = $PDOStatement->fetch($adapter::FETCH_ASSOC))
		{
			$dataSet[] = $row;
		}

		if(empty($dataSet))
		{
			return array();
		}

		$collection = array();
		foreach($dataSet as $element)
		{
			$collection[$element['id']] = $returnedClassType::factory($element);
		}

		return $collection;
	}

	/**
	 * @return mixed
	 */
	abstract public function create();

	/**
	 * @return mixed
	 */
	abstract public function read();

	/**
	 * @return mixed
	 */
	abstract public function update();

	/**
	 * @return mixed
	 */
	abstract public function delete();
}