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

	class PhorthEngine extends PhorthEngine___StackManager
	{
		private $promiseManager;

		private $oracle;

		private $state;

		static public function create($oracle = NULL)
		{
			return new self(new PhorthEngine___Initializer($oracle));
		}

		public function __construct($state)
		{
			$this->promiseManager = PhorthEngine___CompoundPromiseManager::getInstance();

			$this->injectState($state);
		}

		protected function injectState($state)
		{
			$this->changeState($state->getState(), FALSE);
			$this->setOracle($state->getOracle());
			$this->injectStacks($state);
		}

		protected function setOracle($oracle)
		{
			$this->oracle = $oracle;
		}

		protected function getOracle()
		{
			return $this->oracle;
		}

		public function changeState($state, $store = TRUE)
		{
			if ($store)
			{
				$this->storeState();
			}

			if (is_object($this->state))
			{
				$this->state = $this->getState()->mutateState($state);
			}
			else
			{
				$this->state = $state;
			}

//print('State: [');print(implode(':::', array_map('get_class', $this->getStack('__state_scope'))));print(']<br><br>');
			return $this;
		}

		protected function getState()
		{
			return $this->state;
		}

		protected function storeState()
		{
//print('local:['.$local.']'."\n");
			if ($this->state)
			{
				$this->pushDatum($this->getState(), '__state_scope');
			}
		}

		public function restoreState()
		{
			$this->changeState($this->popDatum('__state_scope'), FALSE);

			return $this;
		}

		protected function preserveLocalScope()
		{
			$this->pushDatum(array(
					'__local_stacks' => $this->getStacks($this->getLocalContextHandle()),
					'__stack_selections' => $this->getSelectedStackIds(),
					), '__sub_scope');
			$this->setStacks(PhorthEngine___Initializer::getInitialLocalStacks($this), $this->getLocalContextHandle());
		}

		protected function restoreLocalScope()
		{
			$scope = $this->popDatum('__sub_scope');
			$this->selectStacks($scope['__stack_selections']);
			$this->setStacks($scope['__local_stacks'], $this->getLocalContextHandle());

			return $this;
		}

		public function decideWrapper($args)
		{
			foreach ($args as $arg)
			{
				$this->pushDatum($arg);
			}

//print('{');print_r($this->getStacks());print('}<br>');
			return $this->decidePostwrapper();
		}

		protected function decidePostwrapper()
		{
			return $this->decide();
		}

		protected function decide()
		{
			return $this;
		}

		public function __get($name)
		{
			return $this->__call($name, array());
		}

		public function __call($name, $args)
		{
			try
			{
				return $this->getState()->invoke($this, $name, $args);
			}
			catch (PhorthEngine___EngineException $e)
			{
				return $this->Error(print_r($e, TRUE));
			}
		}
	}

	class PhorthEngine___Initializer extends PhorthEngine
	{
		static public function &getInitialGlobalStacks($engine)
		{
			$initGlobalStacks = array(
					$engine->getResultStackId() => array(),
					$engine->getDataStackId() => array(),
					// Do not touch the following stacks unless
					// you want everything to blow up spectacularly.
					'__sub_scope' => array(),
					'__tail_calls' => array(),
					);

			return $initGlobalStacks;
		}

		static public function &getInitialLocalStacks($engine)
		{
			$initLocalStacks = array(
					$engine->getHeapStackId() => array(),
					// Do not touch the following stacks unless
					// you want everything to blow up spectacularly.
					'__state_scope' => array(),
					'__stack_history' => array(),
					);

			return $initLocalStacks;
		}

		public function __construct($oracle)
		{
			$this->setOracle($oracle);
		}

		protected function getState()
		{
			return PhorthEngine___NormalState::getInstance();
		}

		protected function &getStacks($contextHandle = NULL)
		{
			$initStacks = array(
					$this->getGlobalContextHandle() => self::getInitialGlobalStacks($this),
					$this->getLocalContextHandle() => self::getInitialLocalStacks($this),
					);

			return $initStacks;
		}

		protected function getSelectedStackIds()
		{
			return array(
					$this->getGlobalContextHandle() => $this->getResultStackId(),
					$this->getLocalContextHandle() => $this->getHeapStackId(),
					);
		}

		protected function getSelectedContextHandle($override = NULL)
		{
			return $this->getGlobalContextHandle();
		}
	}

	class PhorthEngine___NormalState
	{
		static private $inst = NULL;

		static private $stackManipPrefixes = array(
				'D' => 'Drop',
				'N' => 'Nip',
				'W' => 'Dup',
				'O' => 'Over',
				'T' => 'Tuck',
				'X' => 'Swap',
				'R' => 'Rot',
				'U' => 'Unrot',
				'd' => 'DDrop',
				'n' => 'DNip',
				'w' => 'DDup',
				'o' => 'DOver',
				't' => 'DTuck',
				'x' => 'DSwap',
				'r' => 'DRot',
				'u' => 'DUnrot',
				);

		static public function getInstance()
		{
			if (! self::$inst)
			{
				self::$inst = new self;
			}

			return self::$inst;
		}

		protected function __construct()
		{
		}

		public function mutateState($newState)
		{
			return $newState;
		}

		public function invoke($engine, $name, $args)
		{
//print($name.'('.implode(', ', $args).')<br>');
//print($name.'('.print_r($args, TRUE).')<br>');
//print('{');print_r($engine->getStacks());print('}<br>');
			if ($name[0] == '_')
			{
				$engine->pushDatum(new PhorthEngine___Promise(substr($name, 1), $args));

				return $engine;
			}

			if (($sep = strpos($name, '__')) !== FALSE)
			{
				$prefix = substr($name, 0, $sep);
				$word = substr($name, $sep + 2);

				if (! $word)
				{
					throw new PhorthEngine___UndefinedWordException($word);
				}

				$st = $engine;

				if ($prefix == 'Promise')
				{
//ob_end_flush();print('yabadumba!');//die();
				//	$name = $word;
				}
				elseif (preg_match('/^Host([0-9]+)$/', $prefix, $matches))
				{
					return $this->invoke($engine, 'NativeCall', array_merge($args, array($matches[1], $word)));
				}
				elseif (preg_match('/^N([0-9]+)$/', $prefix, $matches))
				{
					$st->pushDatum(new PhorthEngine___Promise($word, $args));

					return $st->Repeat($matches[1]);
				}
				elseif (preg_match('/^(['.implode('', array_keys(self::$stackManipPrefixes)).']+)$/', $prefix))
				{
					do
					{
						$m = $prefix[0];
						$prefix = substr($prefix, 1);
	
						$fm = new PhorthEngine___Promise(self::$stackManipPrefixes[$m], $args);
	
						$st = $fm->invokeWith($st);
	
						$args = array();
					} 
					while (strlen($prefix));

					return $this->invoke($st, $word, $args);
				}
				else
				{
					throw new PhorthEngine___InvalidPrefixException($prefix);
				}
			}

			$ruleClassName = 'PhorthEngine__'.$name;

			if (class_exists($ruleClassName))
			{
				$f = new $ruleClassName($engine);
	
				return $f->decideWrapper($args);
			}
			elseif (PhorthEngine___CompoundPromiseManager::getInstance()->existsPromise($name))
			{
//print('herungo!');die();
//				$f = PhorthEngine___CompoundPromiseManager::invokePromiseWith($engine, $name, $args);
//print('[');print_r($f);print(']');die();
				return PhorthEngine___CompoundPromiseManager::getInstance()->invokePromiseWith($engine, $name, $args);
			}

			throw new PhorthEngine___UndefinedWordException($name);
		}

		public function __toString()
		{
			return '['.get_class($this).']';
		}
	}

	abstract class PhorthEngine___SkipState extends PhorthEngine___NormalState
	{
		public function invoke($engine, $name, $args)
		{
			return $engine;
		}
	}

	abstract class PhorthEngine___Word extends PhorthEngine
	{
	}

	abstract class PhorthEngine___MarkerWord extends PhorthEngine
	{
		protected function decide()
		{
			return $this->Error('Fatal error: '.get_class($this).' evaluated.');
		}
	}

?>
