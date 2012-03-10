<?php
abstract class DbCommand extends CDbCommand {
	/**
	 * Creates and executes an INSERT SQL statement with multiple records.
	 * The method will properly escape the column names, and bind the values to be inserted.
	 * @param string $table the table that new rows will be inserted into.
	 * @param array $records sets of values for the records to be inserted. each record has column data in the form name=>value.
	 * @return integer number of rows affected by the execution.
	 */
	public function insertMany($table, $records) {
		$params=array();
		$names=array();
		$placeholders=array();
		$values=array();
		
		foreach ($records as $columns) {
			foreach($columns as $name=>$value) {
				$names[]=$this->getConnection()->quoteColumnName($name);
				if($value instanceof CDbExpression) {
					$placeholders[] = $value->expression;
					foreach($value->params as $n => $v)
						$params[$n] = $v;
				}
				else {
					$placeholders[] = ':' . $name;
					$params[':' . $name] = $value;
				}
			}
			$values[] = '(' . implode(', ', $placeholders) . ')';
		}
		
		$sql='INSERT INTO ' . $this->getConnection()->quoteTableName($table)
			. ' (' . implode(', ',$names) . ') VALUES '
			. implode(', ', $values);
		
		return $this->setText($sql)->execute($params);
	}
}