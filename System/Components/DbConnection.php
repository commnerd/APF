<?php

namespace System\Components;

use Mysqli;

class DbConnection extends AppComponent
{

	protected $connection = null;

	public function __construct($username, $password, $hostname, $dbname, $port) {
		parent::__construct();
        $this->connection = new mysqli($hostname, $username, $password, $dbname, $port);
		if ($this->connection->connect_errno) {
		    echo "Failed to connect to MySQL: (" . $this->connection->connect_errno . ") " . $this->connection->connect_error;
		}
	}

	public function __destruct() {
	   	//close the connection
		$this->connection->close();
	}

    public function getDbInfo($pSQL) {
		$stmt = $this->connection->prepare($pSQL);
	    $stmt->execute();
	    $row = $this->bind_result_array($stmt);
	    if(!$stmt->error)
	    {
	        while($stmt->fetch())
	            $dataArray = $row;

	    }
	    $stmt->close();
	    if (isset($dataArray)) {
	    	return $dataArray;
	    } else {
	    	return;
	    }

    }

    public function getCustomQuery($pSQL,$pTheBindVal) {
		$stmt = $this->connection->prepare($pSQL);
		if (!is_null($pTheBindVal)) {
			call_user_func_array(array($stmt, 'bind_param'), $pTheBindVal);
			//$stmt->bind_param("i", $pTheBindVal);
		}
	    $stmt->execute();

	    // print_r($stmt->error);
	    $stmt->store_result();
	    $row = $this->bind_result_array($stmt);
	    if(!$stmt->error)
	    {

	        while($stmt->fetch())
	            $dataArray = $row;

	    }
	    $stmt->close();
	    if (isset($dataArray)) {
	    	return $dataArray;
	    	unset($dataArray);
	    } else {
	    	return;
	    }

    }

     public function getCustomQueries(DbQuery $query) {
		$pSQL = $query->query;
 		$pTheBindVal = $query->bindings;
		$stmt = $this->connection->prepare($pSQL);
		if (!empty($pTheBindVal)) {
			call_user_func_array(array($stmt, 'bind_param'), $this->_refValues($pTheBindVal));
			//$stmt->bind_param("i", $pTheBindVal);
		}
	    $stmt->execute();

	    // print_r($stmt->error);
	    $stmt->store_result();
	    $row = $this->bind_result_array($stmt);
	    if(!$stmt->error)
	    {

	        while($stmt->fetch()) {

			    foreach( $row as $key=>$value )
			    {
			        $row_tmb[ $key ] = $value;
			    }

	            $dataArray[] = $row_tmb;

			}
	    }
	    $stmt->close();
	    if (isset($dataArray)) {
	    	return $dataArray;
	    	unset($dataArray);
	    } else {
	    	return;
	    }

    }

    public function getRecord($pSQL,$pReqID,$pTheClass,$pTheBindVal) {
		$stmt = $this->connection->prepare($pSQL);
		if (!is_null($pTheBindVal)) {
			call_user_func_array(array($stmt, 'bind_param'), $pTheBindVal);
			//$stmt->bind_param("i", $pTheBindVal);
		}
	    $stmt->execute();
	    $row = $this->bind_result_array($stmt);
	    if(!$stmt->error)
	    {

	        while($stmt->fetch())
	            $dataArray = $row;

	    }
	    $stmt->close();
	    if (isset($dataArray)) {
	    	return $dataArray;
	    } else {
	    	return;
	    }

    }

    public function getRecords($pSQL,$pReqID,$pTheBindVal) {
		$stmt = $this->connection->prepare($pSQL);

		if (!is_null($pTheBindVal)) {
			call_user_func_array(array($stmt, 'bind_param'), $pTheBindVal);
			//$stmt->bind_param("i", $pTheBindVal);
		}
	    $stmt->execute();
	    $row = $this->bind_result_array($stmt);
	    if(!$stmt->error)
	    {
	        while($stmt->fetch())
	            $dataArray[$row[$pReqID]] = $row;
	    }
	    $stmt->close();
	    if (isset($dataArray)) {
	    	return $dataArray;
	    } else {
	    	return;
	    }
    }

    public function addRecord(DbQuery $query) {
		$pSQL = $query->query;
		$pTheBindVal = $query->bindings;
    	$tempBindValArr = implode("||", $pTheBindVal);
    	$tempBindValArr = explode("||", $tempBindValArr);
		//var_dump($pSQL);
		$stmt = $this->connection->prepare($pSQL);
		//var_dump($stmt);
			if (!is_null($pTheBindVal)) {
			//print_r($pTheBindVal);
			$ref    = new \ReflectionClass('mysqli_stmt');
			//print_r($ref);
			$method = $ref->getMethod("bind_param");
			//print_r($method);
			$method->invokeArgs($stmt,$this->_refValues($tempBindValArr));

			//call_user_func_array(array($stmt, 'bind_param'), $pTheBindVal);
			//$stmt->bind_param("i", $pTheBindVal);
		}
		unset($tempBindValArr);
	    $stmt->execute();
	    $newID = $stmt->insert_id;
	    $stmt->close();
	    //echo '<p>New ID: '.$newID.'<p>';
	    return $newID;
    }

    public function updateOrAddRecord($pSQL1,$pSQL2,$pTheBindVal1,$pTheBindVal2) {

    	$tempBindValArr1 = implode("||", $pTheBindVal1);
    	$tempBindValArr1 = explode("||", $tempBindValArr1);

    	$tempBindValArr2 = implode("||", $pTheBindVal2);
    	$tempBindValArr2 = explode("||", $tempBindValArr2);


        // print_r($tempBindValArr1);
        // echo "\n0-----\n";
        // print_r($tempBindValArr2);
        // echo "\n1-----\n";
		// var_dump($pSQL1);
        // echo "\n2-----\n";
		// var_dump($pSQL2);
        // echo "\n3-----\n";
		$stmt = $this->connection->prepare($pSQL1);

		// Return -1 in case of failed query parsing
		if(is_bool($stmt)) {
			throw new \Exception('Failed to properly parse update query.');
		}

        // echo "\n4-----\n";
		// var_dump($stmt);
        // echo "\n5-----\n";
		if (!is_null($pTheBindVal1)) {
        // echo "\n6-----\n";
			// print_r($pTheBindVal1);
        // echo "\n7-----\n";
			$ref    = new \ReflectionClass('mysqli_stmt');
        // echo "\n8-----\n";
			// print_r($ref);
        // echo "\n9-----\n";
			$method = $ref->getMethod("bind_param");
        // echo "\n0-----\n";
			// print_r($method);
        // echo "\n------\n";
        // printf("Error: %s.\n", $this->connection->error);
        // exit;
			$method->invokeArgs($stmt,$tempBindValArr1);

			//call_user_func_array(array($stmt, 'bind_param'), $pTheBindVal);
			//$stmt->bind_param("i", $pTheBindVal);
		}

	    $stmt->execute();

		if ($this->connection->affected_rows == 0) {
			$stmt = $this->connection->prepare($pSQL2);

			if(is_bool($stmt)) {
				throw new \Exception('Failed to properly parse insert query.');
			}
				// var_dump($stmt);
				if (!is_null($pTheBindVal2)) {
					// print_r($pTheBindVal2);
					$ref    = new \ReflectionClass('mysqli_stmt');
					// print_r($ref);
					$method = $ref->getMethod("bind_param");
					// print_r($method);
					$method->invokeArgs($stmt,$tempBindValArr2);

					//call_user_func_array(array($stmt, 'bind_param'), $pTheBindVal);
					//$stmt->bind_param("i", $pTheBindVal);

        // echo "\n-----\n";
        // printf("Error: %s.\n", $this->connection->error);
         // exit;

				}
			    $stmt->execute();
		}

		unset($tempBindValArr);
	    $newID = $stmt->insert_id;
	    $stmt->close();
	    //echo '<p>New ID: '.$newID.'<p>';
	    return $newID;
    }

    public function updateRecord(DbQuery $query) {
		$pSQL = $query->query;
		$pTheBindVal = $query->bindings;
    	$tempBindValArr = implode("||", $pTheBindVal);
    	$tempBindValArr = explode("||", $tempBindValArr);
		$stmt = $this->connection->prepare($pSQL);
		if (!is_null($pTheBindVal)) {
			//print_r($pTheBindVal);
			$ref    = new \ReflectionClass('mysqli_stmt');
			//print_r($ref);
			$method = $ref->getMethod("bind_param");
			//print_r($method);
			$method->invokeArgs($stmt,$this->_refValues($tempBindValArr));

			//call_user_func_array(array($stmt, 'bind_param'), $pTheBindVal);
			//$stmt->bind_param("i", $pTheBindVal);
		}
		unset($tempBindValArr);

	    ////var_dump($stmt);
	    $stmt->execute();
	    // $newID = $stmt->update_id;
	    $stmt->close();
	    //echo '<p>New ID: '.$newID.'<p>';
	    //return $newID;
    }

    public function deleteRecord($pSQL,$pTheBindVal) {
		$stmt = $this->connection->prepare($pSQL);
		if (!is_null($pTheBindVal)) {
			call_user_func_array(array($stmt, 'bind_param'), $this->_refValues($pTheBindVal));
			//$stmt->bind_param("i", $pTheBindVal);
		}
	    $stmt->execute();
    }


	/*
	 * Utility function to automatically bind columns from selects in prepared statements to
	 * an array
	 */
	private function bind_result_array($stmt)
	{
	    $meta = $stmt->result_metadata();
	    $result = array();
	    while ($field = $meta->fetch_field())
	    {
	        $result[$field->name] = NULL;
	        $params[] = &$result[$field->name];
	    }

	    call_user_func_array(array($stmt, 'bind_result'), $params);

	    return $result;
	}

	/**
	 * Returns a copy of an array of references
	 */
	private function getCopy($row)
	{
	    return array_map(function ($a){return $a;}, $row);
	}

//		if (!($stmt = $this->connection->prepare($pSQL))) {
//		     echo "Prepare failed: (" . $this->connection->errno . ") " . $this->connection->error;
//		}

/*
		if (!$stmt->bind_param("s", $this->theTable)) {
		    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
*/

//		if (!$stmt->execute()) {
//		    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
//		}

//		call_user_func_array(array($stmt, 'bind_result'), $bindArray);
/*
		if (!$stmt -> bind_result($a,$b,$c)) {
		    echo "Bind result failed: (" . $stmt->errno . ") " . $stmt->error;
		}
*/
//		$stmt->store_result();

//		echo "Items: ".$stmt->num_rows."<br>";

//		while ($stmt->fetch()) {
			/*
			printf("%s %s %s<br>\n", $a, $b, $c);
			*/
//			echo $bindArray[0]." ".$bindArray[1]." ".$bindArray[2]." ".$bindArray[3]."<br>";
//    	}

//		$stmt->free_result();
	private function _refValues($arr){
	    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
	    {
	        $refs = array();
	        foreach($arr as $key => $value)
	            $refs[$key] = &$arr[$key];
	        return $refs;
	    }
	    return $arr;
	}

}

?>
