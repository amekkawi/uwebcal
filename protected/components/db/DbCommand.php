<?php
abstract class DbCommand extends CDbCommand {
	/**
	 * Creates and executes an INSERT SQL statement with multiple records.
	 * The method will properly escape the column names, and bind the values to be inserted.
	 * @param string $table the table that new rows will be inserted into.
	 * @param array $records sets of values for the records to be inserted. each record
	 *                       has column data in the form name=>value.
	 * @param array $params params in the form (':' . COLUMNNAME => VALUE) that will be used
	 *                      to set a global param for columns that have the same value.
	 * @return integer number of rows affected by the execution.
	 */
	public function insertMany($table, array $records, array $params=array()) {
		$names=array();
		$values=array();
		$first=true;
		
		foreach ($records as $recnum => $columns) {
			if (!$first && count($columns) != count($names))
				throw new CDbException(Yii::t('app', 'Column count for records passed to insertMany must match.'));
				
			$i=0;
			$placeholders = array();
			foreach($columns as $name => $value) {
				if ($first)
					$names[]=$this->getConnection()->quoteColumnName($name);
				elseif ($names[$i] != $this->getConnection()->quoteColumnName($name))
					throw new CDbException(Yii::t('app', 'Column order for records passed to insertMany must match.'));
				
				if($value instanceof CDbExpression) {
					$placeholders[] = $value->expression;
					foreach($value->params as $n => $v) {
						if (isset($params[$n]))
							throw new CDbException(Yii::t('app', 'Param name "{param}" set by a CDbExpression already exists. Make sure they do not use the format (\':\' . COLUMNNAME)', array('{param}' => $n)));
						
						$params[$n] = $v;
					}
				}
				elseif (isset($params[':' . $name]) && $params[':' . $name] === $value) {
					$placeholders[] = ':' . $name;
				}
				else {
					$placeholders[] = $internal = ':c' . $i . '__r' . $recnum;
					
					if (isset($params[$internal]))
						throw new CDbException(Yii::t('app', 'Internal insertMany param name "{param}" already exists. Most likely set by a CDbExpression param. Make sure they do not use a similar format.', array('{param}' => ':c_' . $i . '_' . $recnum)));
					
					$params[$internal] = $value;
				}
				$i++;
			}
			$values[] = $placeholders;
			
			$first=false;
		}
		
		return $this->insertManyInternal($table, $names, $values, $params);
	}
	
	protected function insertManyInternal($table, array $names, array $values, array $params) {
		foreach ($values as $i => $placeholders) {
			$values[$i] = '(' . implode(', ', $placeholders) . ')';
		}
		
		$sql='INSERT INTO ' . $this->getConnection()->quoteTableName($table)
			. ' (' . implode(', ',$names) . ') VALUES '
			. implode(', ', $values);
		
		return $this->setText($sql)->execute($params);
	}
}