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

	class PhorthEngine___SubState extends PhorthEngine___NormalState
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
	}

	abstract class PhorthEngine___Sub extends PhorthEngine___Word
	{
		public function decideWrapper($args, $iter = FALSE)
		{
			if ($iter)
			{
				$this->changeState(PhorthEngine___SubState::getInstance());
//print('IN:'.$this->dumpState());
				$this->preserveLocalScope();
//print('{'.get_class($this).'}<br>');
				return parent::decideWrapper($args)->restoreLocalScope()->restoreState();
			}

			$tailCallFlag = FALSE;

			do
			{
//if(!$tailCallFlag)print('first entry<br>');else print('calling...<br>');
//print_r($st->getStack('__result'));print('<br>');
//if($tailCallFlag)print('boing! ['.get_class($this).']<br>');
				$this->injectState($this->decideWrapper($args, TRUE));
//if($tailCallFlag)print('boboing! ('.get_class($st).')<br>');

//print_r($st->getStacks());print('!<br>');
//print_r($this->getStacks());print('?<br>');
				if ((! $this->isStackEmpty('__tail_calls')) && ($this->peekDatum('__tail_calls') instanceof PhorthEngine___TailCall))
				{
//print('yabadumda!<br>');
					$this->popDatum('__tail_calls');
					$tailCallFlag = TRUE;
				}
				else
				{
					$tailCallFlag = FALSE;
				}
			}
			while ($tailCallFlag);

			return $this;
//print('BEFORE RUN:'.$this->dumpState());
//$r = parent::decideWrapper($args);
//print('BEFORE OUT:'.$r->dumpState());
//$k = $r->restoreLocalScope()->restoreState();
//print('OUT:'.$k->dumpState());
//return $k;
		}
	}

	class PhorthEngine___YieldState extends PhorthEngine___SkipState
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
	}

	class PhorthEngine__Yield extends PhorthEngine___Word
	{
		private $inited = FALSE;

		public function decideWrapper($args)
		{
			$outcome = parent::decideWrapper($args);

			if (! $this->inited)
			{
				$this->inited = TRUE;

				$outcome->changeState(PhorthEngine___YieldState::getInstance(), FALSE);
			}

			return $outcome;
		}
	}

	class PhorthEngine___TailCall
	{
		public function __toString()
		{
			return '[PhorthEngine___TailCall]';
		}
	}

	class PhorthEngine__Tail extends PhorthEngine___Word
	{
		public function decide()
		{
			// teh stack!!! it ish all wroing!
			$this->pushDatum(new PhorthEngine___TailCall, '__tail_calls');
//print_r($this->getStack('__tail_calls'));print('<br>');

			return $this->Yield;
		}
	}

	class PhorthEngine__YieldFail extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->Fail->Yield;
		}
	}

?>
