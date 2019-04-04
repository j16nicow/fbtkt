<?php
//bkmkmntApp.php
//An application logic. Implement overriding of CNicowApp.

//location://docroot/.../(web)/bkmkmntApp.php

require('../CNicowApp.php');

class bkmkmntApp extends CNicowApp
{

	//------------------------------------------------------------------------------
	// const and dest

	public	function __construct()
	{
		parent::__construct();
		echo '#derived construct<br>';
	}

	public	function __destruct()
	{
		echo '#derived destruct<br>';
		parent::__destruct();
	}

	//------------------------------------------------------------------------------
	//Setter...nop
	//Gettter

	public	function getItmName($row)	{ return $this->WORK[$row]['ItmName'];	}
	public	function getGenre($row)		{ return $this->WORK[$row]['Genre'];	}
	public	function getNumber($row)	{ return $this->WORK[$row]['Number'];	}

	public	function getSum()
	{
		$sum = 0;
		global $MAX_ROW;
		for($row=0; $row<$MAX_ROW; $row++)
		{
			$sum += $this->WORK[$row]['Number'];
		}
		return	$sum;
	}

	//------------------------------------------------------------------------------
	//Simple processing

	private	function PostLoadToArrayWork()
	{
		global $MAX_ROW;
		for($row=0; $row<$MAX_ROW; $row++)
		{
			foreach($_POST[$row] as $key => $val)
			{
				if(isset($this->WORK[$row][$key]))
				{
					$this->WORK[$row][$key] = filter_var($val);
				}
			}
		}
	}

	private	function dbInsert()
	{
		$pdo	= $this->dbGet();
		$sql	= 'insert into bkmk values(?,    ?,?,    ?,?,?,    ?,?,?,    ?,?,?,    ?,?,?)';
		$stmt	= $pdo->prepare($sql);
		$res	= $stmt->execute(array( '',
										$this->WORK['KeyCode'], $this->WORK['Category'],
										$this->WORK[0]['ItmName'], $this->WORK[0]['Genre'], $this->WORK[0]['Number'],
										$this->WORK[1]['ItmName'], $this->WORK[1]['Genre'], $this->WORK[1]['Number'],
										$this->WORK[2]['ItmName'], $this->WORK[2]['Genre'], $this->WORK[2]['Number'],
										$this->WORK[3]['ItmName'], $this->WORK[3]['Genre'], $this->WORK[3]['Number']
									)
								);
		return	$res;
	}

	private	function dbSelect()
	{
		$pdo	= $this->dbGet();
		$sql	= 'select * from bkmk where KeyCode = ?';
		$stmt	= $pdo->prepare($sql);
		$stmt->bindValue(1, $this->WORK['KeyCode']);
		$res	= $stmt->execute();
		$row	= $stmt->fetch();
		if($row)
		{
										$this->WORK['DBID'] = $row['id'];
										$this->WORK['KeyCode'] = $row['KeyCode']; $this->WORK['Category'] = $row['Category'];
										$this->WORK[0]['ItmName'] = $row['ItmName0']; $this->WORK[0]['Genre'] = $row['Genre0']; $this->WORK[0]['Number'] = $row['Number0'];
										$this->WORK[1]['ItmName'] = $row['ItmName1']; $this->WORK[1]['Genre'] = $row['Genre1']; $this->WORK[1]['Number'] = $row['Number1'];
										$this->WORK[2]['ItmName'] = $row['ItmName2']; $this->WORK[2]['Genre'] = $row['Genre2']; $this->WORK[2]['Number'] = $row['Number2'];
										$this->WORK[3]['ItmName'] = $row['ItmName3']; $this->WORK[3]['Genre'] = $row['Genre3']; $this->WORK[3]['Number'] = $row['Number3'];
			return TRUE;
		}
		return	FALSE;
	}

	private	function dbUpdate()
	{
		$pdo	= $this->dbGet();
		$sql	=	'update bkmk set KeyCode = ?, Category = ?,' .
					' ItmName0 = ?, Genre0 = ?, Number0 = ?,' .
					' ItmName1 = ?, Genre1 = ?, Number1 = ?,' .
					' ItmName2 = ?, Genre2 = ?, Number2 = ?,' .
					' ItmName3 = ?, Genre3 = ?, Number3 = ?' . ' where id = ?';

		$stmt	= $pdo->prepare($sql);
		$res	= $stmt->execute(array( 
										$this->WORK['KeyCode'], $this->WORK['Category'],
										$this->WORK[0]['ItmName'], $this->WORK[0]['Genre'], $this->WORK[0]['Number'],
										$this->WORK[1]['ItmName'], $this->WORK[1]['Genre'], $this->WORK[1]['Number'],
										$this->WORK[2]['ItmName'], $this->WORK[2]['Genre'], $this->WORK[2]['Number'],
										$this->WORK[3]['ItmName'], $this->WORK[3]['Genre'], $this->WORK[3]['Number'], $this->WORK['DBID']
									)
								);
		return	$res;
	}

	private	function dbDelete()
	{
		$pdo	= $this->dbGet();
		$sql	= 'delete from bkmk where id = ?';
		$stmt	= $pdo->prepare($sql);
		$stmt->bindValue(1, $this->WORK['DBID']);
		$res	= $stmt->execute();
		return	$res;
	}

	//------------------------------------------------------------------------------
	//override

	protected	function OnDump()	{}

	protected	function OnCreateWorkArea()
	{
		$clm = array( 'ItmName' => '', 'Genre' => '', 'Number' => 0 );

		$this->WORK['KeyCode']	= 0;
		$this->WORK['Category']	= '';
		$this->WORK['DBID']		= 0;

		global $MAX_ROW;
		for($row=0; $row<$MAX_ROW; $row++)
		{
			$this->WORK[$row] = $clm;
		}
	}

	protected	function OnCreate($step_idx)
	{
		$this->MSG = '';
		$cleardata = FALSE;
		switch($step_idx)
		{
			case 0:
				$this->PostLoadToMonoWork();
				$cnt = $this->dbCountRecord('bkmk', 'KeyCode =', $this->WORK['KeyCode']);
				($cnt)? $this->MSG = 'key duplicates' : $this->incStep();
				break;
			case 1:
				$this->PostLoadToMonoWork();
				$this->incStep();
				break;
			case 2:
				$this->PostLoadToArrayWork();
				$this->incStep();
				break;
			case 3:
				($this->dbInsert())? $cleardata = TRUE : $this->MSG = 'failed insert db';
				break;
			default:
				throw new Exception('Bad Step -Create-');
		}
		return	$cleardata;
	}

	protected	function OnRead($step_idx)
	{
		$this->MSG = '';
		$cleardata = FALSE;
		switch($step_idx)
		{
			case 0:
				$this->PostLoadToMonoWork();
				if($this->dbSelect()){
					$this->incStep();	$this->incStep();	$this->incStep();
				}else{
					$this->MSG = 'key nothing';
				}
				break;
			case 3:
				$cleardata = TRUE;
				break;
			default:
				throw new Exception('Bad Step -Read-');
		}
		return	$cleardata;
	}

	protected	function OnUpdate($step_idx)
	{
		$this->MSG = '';
		$cleardata = FALSE;
		switch($step_idx)
		{
			case 0:
				$this->PostLoadToMonoWork();
				if($this->dbSelect()){
					$this->incStep();
				}else{
					$this->MSG = 'key nothing';
				}
				break;
			case 1:
				$this->PostLoadToMonoWork();
				$this->incStep();
				break;
			case 2:
				$this->PostLoadToArrayWork();
				$this->incStep();
				break;
			case 3:
				($this->dbUpdate())? $cleardata = TRUE : $this->MSG = 'failed update db';
				break;
			default:
				throw new Exception('Bad Step -Update-');
		}
		return	$cleardata;
	}

	protected	function OnDelete($step_idx)
	{
		$this->MSG = '';
		$cleardata = FALSE;
		switch($step_idx)
		{
			case 0:
				$this->PostLoadToMonoWork();
				if($this->dbSelect()){
					$this->incStep();	$this->incStep();	$this->incStep();
				}else{
					$this->MSG = 'key nothing';
				}
				break;
			case 3:
				($this->dbDelete())? $cleardata = TRUE : $this->MSG = 'failed delete db';
				break;
			default:
				throw new Exception('Bad Step -Delete-');
		}
		return	$cleardata;
	}

}//class eof

/*
*/