<?php
/**
 * Created by PhpStorm.
 * User: minglong
 * Date: 15/12/18
 * Time: 上午11:46
 */
namespace app\models;

class JLock {
	private $_lock;
	private $_key;

	public function __construct($key)
	{
		$this->_key = $key;
	}

	public function start()
	{
		$this->_lock = new JPHPLock(\Yii::$app->basePath.DIRECTORY_SEPARATOR.'jlock'.DIRECTORY_SEPARATOR, $this->_key );
		$this->_lock->startLock ();
		$status = $this->_lock->Lock ();
		if (! $status) {
			exit ( "lock error" );
		}
	}

	public function end()
	{
		$this->_lock->unlock ();
		$this->_lock->endLock ();
	}
} 