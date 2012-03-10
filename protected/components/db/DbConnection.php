<?php
class DbConnection extends CDbConnection {
	
	public $commandDriverMap = array(
		'pgsql'=>'PgsqlDbCommand',    // PostgreSQL
		'mysqli'=>'MysqlDbCommand',   // MySQL
		'mysql'=>'MysqlDbCommand',    // MySQL
		'sqlite'=>'SqliteDbCommand',  // sqlite 3
		'sqlite2'=>'SqliteDbCommand', // sqlite 2
		'mssql'=>'MssqlDbCommand',    // Mssql driver on windows hosts
		'dblib'=>'MssqlDbCommand',    // dblib drivers on linux (and maybe others os) hosts
		'sqlsrv'=>'MssqlDbCommand',   // Mssql
		'oci'=>'OciDbCommand',        // Oracle driver
	);
	
	/**
	 * Creates a command for execution.
	 * @param mixed $query the DB query to be executed. This can be either a string representing a SQL statement,
	 * or an array representing different fragments of a SQL statement. Please refer to {@link CDbCommand::__construct}
	 * for more details about how to pass an array as the query. If this parameter is not given,
	 * you will have to call query builder methods of {@link CDbCommand} to build the DB query.
	 * @return CDbCommand the DB command
	 */
	public function createCommand($query=null)
	{
		$driver = $this->getDriverName();
		if(isset($this->commandDriverMap[$driver])) {
			$class = $this->commandDriverMap[$driver];
			
			Yii::trace("Creating DbCommand: $class", 'application.components.db.DbConnection');
			
			return new $class($this, $query);
		}
		else
			throw new CDbException(Yii::t('application.db','DbConnection does not support {driver} database.',
				array('{driver}'=>$driver)));
	}
}