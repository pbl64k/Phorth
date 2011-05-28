<?php

	/*
		Copyright 2010, 2011 Pavel Lepin

		This file is part of Phorth.
		
		Phorth is free software: you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
		(at your option) any later version.
		
		Phorth is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.
		
		You should have received a copy of the GNU General Public License
		along with Phorth.  If not, see <http://www.gnu.org/licenses/>.
	*/

	class PhorthEngine___CompoundPromiseManager
	{
		static private $inst = NULL;

		private $promiseList = array();

		static public function getInstance()
		{
			if (! self::$inst)
			{
				self::$inst = new self;
			}

			return self::$inst;
		}

		private function __construct()
		{
		}

		public function addPromise($name, $promise)
		{
			$this->promiseList[$name] = $promise;
		}

		public function addAnonymousPromise($promise)
		{
			$idChars = '_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVXYZ';

			$name = 'Promise__c0';

			while ($this->existsPromise($name))
			{
				$name .= $idChars[rand(0, strlen($idChars) - 1)];
			}

			$this->addPromise($name, $promise);

			return $name;
		}

		public function destroyPromise($name)
		{
			unset($this->promiseList[$name]);
		}

		public function existsPromise($name)
		{
			return isset($this->promiseList[$name]);
		}

		public function invokePromiseWith($engine, $name, $args)
		{
			return $this->promiseList[$name]->invokeWith($engine, $args);
		}
	}

	class PhorthEngine___Promise
	{
		private $word;
		private $args;

		public function __construct($word, $args)
		{
			$this->word = $word;
			$this->args = $args;
		}

		protected function getWord()
		{
			return $this->word;
		}

		protected function getArgs()
		{
			return $this->args;
		}

		public function invokeWith($object, $args = array())
		{
			return call_user_func_array(array($object, $this->getWord()),
					array_merge($this->getArgs(), $args));
		}

		public function getInvocationString()
		{
			return $this->getWord().'('.
					implode(',', array_map(
					create_function('$a', 'return var_export($a, TRUE);'),
					$this->getArgs())).')';
		}

		// unsupported before 5.3.0
		/*
		static public function __callStatic($name, $args)
		{
			return new PhorthEngine___Promise($name, $args);
		}
		*/

		public function __toString()
		{
			return get_class($this).': ['.$this->getInvocationString().']';
		}
	}

	class PhorthEngine___CompoundPromise extends PhorthEngine___Promise
	{
		private $promiseList;

		private $lId = NULL;

		public function __construct()
		{
			$this->promiseList = array();
		}

		public function __destruct()
		{
			if ($this->lId)
			{
				PhorthEngine___CompoundPromiseManager::getInstance()->destroyPromise($this->lId);
			}
		}

		protected function getWord()
		{
			return $this->lId;
		}

		protected function getArgs()
		{
			return array();
		}

		public function invokeWith($object, $args = array())
		{
			foreach($this->promiseList as $promise)
			{
				$st = clone($promise->invokeWith($object, $args));
//ob_end_flush();print_r($st);print('<br>');

				$object = $st;

				$args = array();
			}

			return $st;
		}

		public function getInvocationString()
		{
			if (! $this->lId)
			{
				$this->lId = PhorthEngine___CompoundPromiseManager::getInstance()->addAnonymousPromise($this);
			}

			return $this->getWord().'('.
					implode(',', array_map(
					create_function('$a', 'return var_export($a, TRUE);'),
					$this->getArgs())).')';
		}

		public function addPromise($name, $args)
		{
			array_push($this->promiseList, new PhorthEngine___Promise($name, $args));
		}
	}

	class PhorthEngine___PromiseState extends PhorthEngine___SkipState
	{
		static private $inst = NULL;

		static public function getInstance()
		{
			if (! self::$inst)
			{
				self::$inst = new self;
			}

			return self::$inst;
		}

		public function invoke($engine, $name, $args)
		{
			if ($name == 'Esimorp')
			{
				return $engine->restoreState();
			}

			$engine->peekDatum()->addPromise($name, $args);

			return $engine;
		}
	}

	class PhorthEngine__Promise extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum(new PhorthEngine___CompoundPromise);

			return $this->changeState(PhorthEngine___PromiseState::getInstance());
		}
	}

	class PhorthEngine__Esimorp extends PhorthEngine___MarkerWord
	{
	}

	class PhorthEngine__Call extends PhorthEngine___Word
	{
		protected function decide()
		{
			return $this->popDatum()->invokeWith($this);
		}
	}

	class PhorthEngine__CallIfApplicable extends PhorthEngine___Word
	{
		protected function decide()
		{
			$f = $this->popDatum();

			if ($f instanceof PhorthEngine___Promise)
			{
				return $this->Call($f);
			}
			
			$this->pushDatum($f);

			return $this;
		}
	}

?>
