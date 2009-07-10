<?php

/******* NOT TESTED *********/

// pulled from http://www.weberdev.com/get_example-4597.html
class db_connect{
	var $sid, $connection;
	var $query;
	var $host;
	var $total_record, $rec_position;
	var $total_fields, $field_name;

	/*This function connect to the database . This function is called whenever object is created. */
	function db_connect() {
		$this->host="".ORCL_HOST."";
		$this->sid="(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=".ORCL_HOST.")(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=".ORCL_SERVICE_NAME.")))";
		$this->connection = ocilogon(ORCL_USER_NAME,ORCL_PASSWORD,$this->sid) or die ($this->get_error_msg($this->connection,"Problem while connecting to ".$this->sid." server..."));//username,password,sid
	}

	/*This function query at database */
	function db_query($query_str="") {
		$this->sql=$query_str;
		$this->rec_position=0;
		// if($query_str==""){
		//   $query_str=$this->query_stmt;
		//}
		$this->query = @ociparse($this->connection, $query_str);
		ociexecute($this->query)or die($this->get_error_msg($this->query ,"Query Error : ".$query_str));
	}

	/*This function query at database which returns TRUE if SUCCESSFUL and FALSE if UNSUCCESSFUL */
	function db_query_return($query_str="",$db="") {
		if($query_str==""){
			$query_str=$this->query_stmt;
		}
		$this->query = ociparse($this->connection, $query_str);
		if($db=="Default") {
			return ociexecute($this->query,OCI_DEFAULT);
		} else {
			return ociexecute($this->query);
		}
	}

	function selectBox($sql='',$selected='',$name='select',$multiple='',$size='',$parameter=NULL) {
		$this->db_query($sql);
		$output="<select name='$name' $multiple $size $parameter>";
		$output.="<option value=''>--Select--</option>";
		while($result=$this->db_fetch_array())
		{
			if($result[0]==trim($selected)) {
				$a="selected";
			} else {
				$a="";
			}
			$output.="<option value=".$result[0]." $a>".$result[1]."</option>";
		}
		$output.="</select>";
		echo $output;
		unset($output);
	}

	function db_fetch_val() {
		$result=$this->db_fetch_array(0);
		return $result[0];
	}

	function gen_id($table_name='',$field='',$increment='') {
		if(empty($increment)) {
			$increment=1;
		} else {
			$increment=$increment;
		}
		$sql="select nvl(max($field),0) + $increment from $table_name";
		$this->db_query($sql);
		return $this->db_fetch_val();
	}

	function db_fetch_array($fetch_type=0,$db="DEFAULT") {
		$result=@oci_fetch_array($this->query,OCI_BOTH+OCI_RETURN_NULLS);

		if(!is_array($result))
		return false;
		$this->total_field=OCINumCols($this->query);
		if($db=="DEFAULT"){
			foreach($result as $key=>$val){
				$result[$key]=trim($val);
				$result[$key]=trim(htmlspecialchars($val));
			}
		}
		return $result;
	}

	function get_field_name($i) {
		return OCIColumnName($this->query, $i+1);
	}

	function get_num_fields() {
		return @ocinumcols($this->query);
	}

	function get_field_type($i, $sql="") {
		return ocicolumntype($this->query, $i+1);
	}

	function get_field_size($i, $sql="") {
		return ocicolumnsize($this->query, $i+1);
	}

	function total_record() {
		return oci_num_rows($this->query);
	}

	function free() {
		ocifreestatement($this->query);
		ocilogoff($this->connection);
		unset($this);
	}

	function db_fetch_tr($css="",$colname='y',$add='y',$update='y',$delete='y') {
		if($css!="") {
			$css_val="class=".$css;
		}
		if(!empty($colname)) {
			echo "<tr $css_val>";
			for ($i=0; $i< $this->get_num_fields(); $i++) {
				echo "<td>".$this->get_field_name($i)."<td>";
			}
			//echo "<td>Update<td>Delete";
			echo "</tr>";
		}
		while($result=$this->db_fetch_array(1)) {
			echo "<tr $css_val>";
			for ($j=0; $j<$this->get_num_fields(); $j++) {
				$cname=$this->get_field_name($j);
				echo "<td>".$result[$cname]." <td>";
			}
			echo "</tr>";
		}

	}

	function get_error_msg($error_no,$msg="") {
		$log_msg=NULL;
		$error_msg="<b>Custom Error :</b> <pre><font color=red>\n\t".ereg_replace(",",",\n\t",$msg)."</font></pre>";
		$error_msg.="<b><i>System generated Error :</i></b>";
		$error_msg.="<font color=red><pre>";
		foreach(ocierror($error_no) as $key=>$val){
			$log_msg.="$key :  ".$val."\n";
			$error_msg.="$key : $val \n";
		}

		$error_msg.="</pre></font>";
		return $error_msg;
	}

	function get_error_msg_array($error_no) {
		return ocierror($error_no);
	}
}
?>