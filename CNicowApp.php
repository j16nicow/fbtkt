<?php
//CNicowApp.php
//Application base class. Template of the procedure. TemplateMethod of the GoF.

//location://docroot/.../CNicowApp.php

class CNicowApp
{
	private		$LSID;	//LocalSessionID;
	protected	$WORK = array();
	private		$DB;	// use ::dbGet()
	protected	$MSG;

	private		$ModeName	= array('Create', 'Read', 'Update', 'Delete');
	private		$StepName	= array('Search', 'Entry', 'Entry', 'Agree');

	//------------------------------------------------------------------------------
	// const and dest

	public	function __construct()
	{
		echo '#base construct<br>';
		session_start();
		//Generate LSID // bad:	See ID. New ID is made with screen update. Old one is left unused // good:	Input in plural tabs.
		$this->LSID = ($tmp = filter_input(INPUT_POST, 'LSID'))? $tmp : date('YmdHis') . '-' . rand();
		if(isset($_SESSION[$this->LSID]))
		{
			$this->WORK = $_SESSION[$this->LSID];
		}else{
			$this->CreateWorkArea( 0 );
		}
		$this->DB	= NULL;
		$this->MSG	= '';
		echo 'debug POSTING : ';	print_r($_POST);														echo '<br>';
		echo 'debug SESSION : ';	(isset($_SESSION[$this->LSID]))? print_r($_SESSION[$this->LSID]) : '';	echo '<br>';
		echo 'debug WORK : ';		print_r($this->WORK);													echo '<br>';
	}

	public	function __destruct()
	{
		echo '#base destruct<br>';
		unset($_SESSION[$this->LSID]);
		$_SESSION[$this->LSID] = $this->WORK;
	}

	//------------------------------------------------------------------------------
	//Setter...nop
	//Gettter

	public	function getModeName()	{ return $this->ModeName[ $this->WORK['ModeIdx'] ]; }
	public	function getStepName()	{ return $this->StepName[ $this->WORK['StepIdx'] ]; }

	public	function getIsKeyDis()	{ return ($this->WORK['StepIdx'] === 0)? '' : ' disabled '; }
	public	function getIsHedDis()	{ return ($this->WORK['StepIdx'] === 1)? '' : ' disabled '; }
	public	function getIsBodDis()	{ return ($this->WORK['StepIdx'] === 2)? '' : ' disabled '; }
	public	function getIsTilDis()	{ return ($this->WORK['StepIdx'] === 3)? '' : ' disabled '; }
	public	function getIsBakDis()	{ return ($this->WORK['StepIdx'] === 0)? ' disabled ' : ''; }

	public	function getMessage()	{ return $this->MSG; }

	public	function getLSID()		{ return '<input type="hidden" name="LSID" value="' . $this->LSID . '" />';}
	public	function getLSIDtest()	{ return $this->LSID;}

	public	function __get($val_name)
	{
		if(isset($this->WORK[$val_name]))
		{
			return	$this->WORK[$val_name];
		}
		return	'';
	}

	//------------------------------------------------------------------------------
	//Simple processing

	public	function Dump()
	{
		echo 'debug DUMP : ' . 'LSID [' . $this->LSID . '] ';	print_r($this->WORK);	echo '<br>';
		$this->OnDump();
	}

	protected	function OnDump()	{}

	private	function CreateWorkArea($new_mode)
	{
		unset($this->WORK);
		$this->WORK = array('ModeIdx'=>0, 'StepIdx'=>0);
		$this->WORK['ModeIdx'] = $new_mode;
		$this->OnCreateWorkArea();
	}

	protected	function OnCreateWorkArea(){}

	protected	function PostLoadToMonoWork()
	{
		foreach($_POST as $key => $tmp)
		{
			if(isset($this->WORK[$key]))
				$this->WORK[$key] = filter_input(INPUT_POST, $key);
		}
	}

	private		function incMode($mode_idx)	{ return ++$mode_idx % 4; }	//[4] ... mode count C,R,U,D=4
	protected	function incStep()			{ return ++$this->WORK['StepIdx']; }
	protected	function decStep()			{ return --$this->WORK['StepIdx']; }
	protected	function rstStep()			{ return $this->WORK['StepIdx'] = 0; }

	protected	function dbGet()
	{
		if($this->DB)	return	$this->DB;
		return	$this->DB = new PDO ('mysql:dbname=agnaktordb; host=localhost; port=3306; charset=utf8', 'root', '',
							[
								PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
							//	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
							]	);
	}

	protected	function dbCountRecord($table_name, $key_name, $key_value)
	{
		$pdo	= $this->dbGet();
		$sql	= 'select count(id) as cnt from ' . $table_name . ' where ' . $key_name . ' ?';	//echo '----' . $sql . '----<br>';
		$stmt	= $pdo->prepare($sql);
		$stmt->bindValue(1, $key_value);
		$res	= $stmt->execute();																//echo '----' . $res . '----<br>';
		$row	= $stmt->fetch();																//echo '----'; print_r($row); echo '----<br>';
		return	$row['cnt'];
	}

	//------------------------------------------------------------------------------
	//Main processing

	public	function Execute()
	{
		//throw new Exception('throw test');
		$MIDX = $this->WORK['ModeIdx'];
		$SIDX = $this->WORK['StepIdx'];
		//start up test
		//Example:	user check
		//Example:	login check		throw new Exception('login err');
		//Example:	browser check	throw new Exception('browser err');		Hint	$_SERVER['HTTP_USER_AGENT']
		//Example:	busy check (Host)
		//change mode
		if(filter_input(INPUT_POST, 'ModeEvent'))
		{
			$this->CreateWorkArea($this->incMode($MIDX));
			return;
		}
		//step back
		if(filter_input(INPUT_POST, 'BackEvent'))
		{
			switch($MIDX)
			{
				case 0://create
				case 2://update
					$this->decStep();
					break;
				case 1://read
				case 3://delete
					$this->rstStep();
					$this->CreateWorkArea($MIDX);
					break;
				default:
					throw new Exception('Bad Mode -BACK-');
			}
			return;
		}
		//post check
		if(!filter_input(INPUT_POST, 'StepEvent'))	//[!]
			return;
		//main procedure
		$cld = FALSE;
		switch($MIDX)
		{
			case 0:
				$cld = $this->OnCreate($SIDX);
				break;
			case 1:
				$cld = $this->OnRead($SIDX);
				break;
			case 2:
				$cld = $this->OnUpdate($SIDX);
				break;
			case 3:
				$cld = $this->OnDelete($SIDX);
				break;
			default:
				throw new Exception('Bad Mode -STEP-');
		}
		if($cld)
			$this->CreateWorkArea($MIDX);
	}

	protected	function OnCreate	($step_idx)	{ return TRUE; }
	protected	function OnRead		($step_idx)	{ return TRUE; }
	protected	function OnUpdate	($step_idx)	{ return TRUE; }
	protected	function OnDelete	($step_idx)	{ return TRUE; }

}//eof class
/*
*/
