<?php
	class DBController
	{
		private $host = "localhost";
		private $user = "rektroth";
		private $password = "Q?HIn3?.0*.z}%dV";
		private $database = "piratedb";
		private $conn;
		
		function __construct()
		{
			$this->conn = $this->connectDB();
		}	
		
		function connectDB()
		{
			$conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
			return $conn;
		}
		
		function runBaseQuery($query)
		{
			$result = mysqli_query($this->conn, $query);
			
			while($row = mysqli_fetch_assoc($result))
			{
				$resultSet[] = $row;
			}		
			
			if(!empty($resultSet))
			{
				return $resultSet;
			}
		}
		
		function runQuery($query, $paramType, $paramValueArray)
		{
			$sql = $this->conn->prepare($query);
			$this->bindQueryParams($sql, $paramType, $paramValueArray);
			$sql->execute();
			$result = $sql->get_result();
			
			if ($result->num_rows > 0)
			{
				while($row = $result->fetch_assoc())
				{
					$resultSet[] = $row;
				}
			}
			
			if (!empty($resultSet))
			{
				return $resultSet;
			}
		}
		
		function bindQueryParams($sql, $paramType, $paramValueArray)
		{
			$paramValueReference[] = &$paramType;
			
			for ($i = 0; $i < count($paramValueArray); $i++)
			{
				$paramValueReference[] = &$paramValueArray[$i];
			}
			
			call_user_func_array(array($sql, "bind_param"), $paramValueReference);
		}
		
		function insert($query, $paramType, $paramValueArray)
		{
			$sql = $this->conn->prepare($query);
			$this->bindQueryParams($sql, $paramType, $paramValueArray);
			$sql->execute();
		}
		
		function update($query, $paramType, $paramValueArray)
		{
			$sql = $this->conn->prepare($query);
			$this->bindQueryParams($sql, $paramType, $paramValueArray);
			$sql->execute();
		}
	}
?>