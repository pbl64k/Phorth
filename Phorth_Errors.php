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

	class PhorthEngine___Fail
	{
		public function __toString()
		{
			return '['.get_class($this).']';
		}
	}

	class PhorthEngine___Error
	{
		public $error;

		function __construct($error)
		{
			$this->error = $error;
		}

		public function __toString()
		{
			return '['.get_class($this).']: '.$this->error;
		}
	}

	abstract class PhorthEngine___EngineException extends Exception
	{
	}

	class PhorthEngine___StackUnderflowException extends
			PhorthEngine___EngineException
	{
		public $message = 'stack underflow exception';

		public $stackName;

		public $stateDescription;

		function __construct($stackName, $description)
		{
			$this->stackName = $stackName;
			$this->stateDescription = $description;
		}
	}

	class PhorthEngine___InvalidContextException extends
			PhorthEngine___EngineException
	{
		public $message = 'invalid context exception';

		public $context;

		function __construct($context)
		{
			$this->context = $context;
		}
	}

	class PhorthEngine___InvalidPrefixException extends
			PhorthEngine___EngineException
	{
		public $message = 'invalid prefix exception';

		public $badPrefix;

		function __construct($prefix)
		{
			$this->badPrefix = $prefix;
		}
	}

	class PhorthEngine___UndefinedWordException extends
			PhorthEngine___EngineException
	{
		public $message = 'undefined word exception';

		public $word;

		function __construct($word)
		{
			$this->word = $word;
		}
	}

	class PhorthEngine___DivisionByZeroException extends
			PhorthEngine___EngineException
	{
		public $message = 'division by zero exception';

		public $denominator;

		function __construct($denom)
		{
			$this->denominator = $denom;
		}
	}

	class PhorthEngine__Error extends PhorthEngine___Word
	{
		public function decide()
		{
			return $this->Terminate(new PhorthEngine___Error($this->popDatum()));
		}
	}

	class PhorthEngine__Fail extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum(new PhorthEngine___Fail);

			return $this;
		}
	}

	class PhorthEngine__IsFail extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum($this->popDatum() instanceof PhorthEngine___Fail);

			return $this;
		}
	}

	class PhorthEngine__IsNotFail extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->IsFail->BNot;
		}
	}

?>
