<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Nosql;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\Exception\PrepareStatementFailureException;
use Joomla\Database\FetchOrientation;
use Joomla\Database\ParameterType;
use Joomla\Database\Query\PreparableInterface;
use Joomla\Database\StatementInterface;

/**
 * Test class JDatabase.
 *
 * @since  1.0
 */
class NosqlDriver extends DatabaseDriver
{
	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $name = 'nosql';

	/**
	 * The character(s) used to quote SQL statement names such as table names or field names,
	 * etc. The child classes should define this as necessary.  If a single character string the
	 * same character is used for both sides of the quoted name, else the first character will be
	 * used for the opening quote and the second for the closing quote.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $nameQuote = '[]';

	/**
	 * The null or zero representation of a timestamp for the database driver.  This should be
	 * defined in child classes to hold the appropriate value for the engine.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $nullDate = '1BC';

	/**
	 * @var    string  The minimum supported database version.
	 * @since  1.0
	 */
	protected static $dbMinimum = '12.1';

	/**
	 * Connects to the database if needed.
	 *
	 * @return  void  Returns void if the database connected successfully.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function connect()
	{
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return  boolean  True if connected to the database engine.
	 *
	 * @since   1.0
	 */
	public function connected()
	{
		return true;
	}

	/**
	 * Disconnects the database.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function disconnect()
	{
	}

	/**
	 * Drops a table from the database.
	 *
	 * @param   string   $table     The name of the database table to drop.
	 * @param   boolean  $ifExists  Optionally specify that the table must exist before it is dropped.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function dropTable($table, $ifExists = true)
	{
		return $this;
	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * @param   string   $text   The string to be escaped.
	 * @param   boolean  $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  string   The escaped string.
	 *
	 * @since   1.0
	 */
	public function escape($text, $extra = false)
	{
		return $extra ? "/$text//" : "-$text-";
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   1.0
	 */
	protected function fetchArray($cursor = null)
	{
		return array();
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   1.0
	 */
	protected function fetchAssoc($cursor = null)
	{
		return array();
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param   mixed   $cursor  The optional result set cursor from which to fetch the row.
	 * @param   string  $class   The class name to use for the returned row object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   1.0
	 */
	protected function fetchObject($cursor = null, $class = 'stdClass')
	{
		return new $class;
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function freeResult($cursor = null)
	{
		return;
	}

	/**
	 * Get the number of affected rows for the previous executed SQL statement.
	 *
	 * @return  integer  The number of affected rows.
	 *
	 * @since   1.0
	 */
	public function getAffectedRows()
	{
		return 0;
	}

	/**
	 * Method to get the database collation in use by sampling a text field of a table in the database.
	 *
	 * @return  mixed  The collation in use by the database or boolean false if not supported.
	 *
	 * @since   1.0
	 */
	public function getCollation()
	{
		return false;
	}

	/**
	 * Method to get the database connection collation in use by sampling a text field of a table in the database.
	 *
	 * @return  mixed  The collation in use by the database connection (string) or boolean false if not supported.
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	public function getConnectionCollation()
	{
		return false;
	}

	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @param   resource  $cursor  An optional database cursor resource to extract the row count from.
	 *
	 * @return  integer   The number of returned rows.
	 *
	 * @since   1.0
	 */
	public function getNumRows($cursor = null)
	{
		return 0;
	}

	/**
	 * Get the current query object or a new DatabaseQuery object.
	 *
	 * @param   boolean  $new  False to return the current query object, True to return a new DatabaseQuery object.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   1.0
	 */
	public function getQuery($new = false)
	{
		return new class($this) extends DatabaseQuery
		{
			protected $bounded = array();

			public function bind($key = null, &$value = null, $dataType = ParameterType::STRING, $length = 0, $driverOptions = [])
			{
				// Case 1: Empty Key (reset $bounded array)
				if (empty($key))
				{
					$this->bounded = array();

					return $this;
				}

				// Case 2: Key Provided, null value (unset key from $bounded array)
				if (is_null($value))
				{
					if (isset($this->bounded[$key]))
					{
						unset($this->bounded[$key]);
					}

					return $this;
				}

				// Case 3: Simply add the Key/Value into the bounded array
				$this->bounded[$key] = &$value;

				return $this;
			}

			public function clear($clause = null)
			{
				switch ($clause)
				{
					case null:
						$this->bounded = array();
						break;
				}

				return parent::clear($clause);
			}

			public function &getBounded($key = null)
			{
				if (empty($key))
				{
					return $this->bounded;
				}

				if (isset($this->bounded[$key]))
				{
					return $this->bounded[$key];
				}
			}
		};
	}

	/**
	 * Retrieves field information about the given tables.
	 *
	 * @param   string   $table     The name of the database table.
	 * @param   boolean  $typeOnly  True (default) to only return field types.
	 *
	 * @return  array  An array of fields by table.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		return array();
	}

	/**
	 * Shows the table CREATE statement that creates the given tables.
	 *
	 * @param   mixed  $tables  A table name or a list of table names.
	 *
	 * @return  array  A list of the create SQL for the tables.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function getTableCreate($tables)
	{
		return array();
	}

	/**
	 * Retrieves field information about the given tables.
	 *
	 * @param   mixed  $tables  A table name or a list of table names.
	 *
	 * @return  array  An array of keys for the table(s).
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function getTableKeys($tables)
	{
		return array();
	}

	/**
	 * Method to get an array of all tables in the database.
	 *
	 * @return  array  An array of all the tables in the database.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function getTableList()
	{
		return array();
	}

	/**
	 * Get the version of the database connector
	 *
	 * @return  string  The database connector version.
	 *
	 * @since   1.0
	 */
	public function getVersion()
	{
		return '12.1';
	}

	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 *
	 * @return  integer  The value of the auto-increment field from the last inserted row.
	 *
	 * @since   1.0
	 */
	public function insertid()
	{
		return 0;
	}

	/**
	 * Locks a table in the database.
	 *
	 * @param   string  $tableName  The name of the table to unlock.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function lockTable($tableName)
	{
		return $this;
	}

	/**
	 * Execute the SQL statement.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function execute()
	{
		return false;
	}

	/**
	 * Renames a table in the database.
	 *
	 * @param   string  $oldTable  The name of the table to be renamed
	 * @param   string  $newTable  The new name for the table.
	 * @param   string  $backup    Table prefix
	 * @param   string  $prefix    For the table - used to rename constraints in non-mysql databases
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
	{
		return $this;
	}

	/**
	 * Select a database for use.
	 *
	 * @param   string  $database  The name of the database to select for use.
	 *
	 * @return  boolean  True if the database was successfully selected.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function select($database)
	{
		return false;
	}

	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function setUtf()
	{
		return false;
	}

	/**
	 * Test to see if the connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   1.0
	 */
	public static function isSupported()
	{
		return true;
	}

	/**
	 * Method to commit a transaction.
	 *
	 * @param   boolean  $toSavepoint  If true roll back to savepoint
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function transactionCommit($toSavepoint = false)
	{
	}

	/**
	 * Method to roll back a transaction.
	 *
	 * @param   boolean  $toSavepoint  If true roll back to savepoint
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function transactionRollback($toSavepoint = false)
	{
	}

	/**
	 * Method to initialize a transaction.
	 *
	 * @param   boolean  $asSavepoint  If true start as savepoint
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function transactionStart($asSavepoint = false)
	{
	}

	/**
	 * Unlocks tables in the database.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function unlockTables()
	{
		return $this;
	}

	/**
	 * Prepares a SQL statement for execution
	 *
	 * @param   string  $query
	 *
	 * @return  StatementInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  PrepareStatementFailureException
	 */
	protected function prepareStatement(string $query): StatementInterface
	{
		return new class implements StatementInterface
		{
			public function bindParam($parameter, &$variable, $dataType = \PDO::PARAM_STR, $length = null, $driverOptions = null)
			{
				return true;
			}

			public function closeCursor()
			{
				return true;
			}

			public function errorCode()
			{
				return '';
			}

			public function errorInfo()
			{
				return [];
			}

			public function execute($parameters = null)
			{
				return true;
			}

			public function fetch($fetchStyle = null, $cursorOrientation = FetchOrientation::NEXT, $cursorOffset = 0)
			{
				return;
			}

			public function fetchObject($className = null, $constructorArgs = null)
			{
				return new $className($constructorArgs);
			}

			public function rowCount()
			{
				return 0;
			}
		};
	}
}
