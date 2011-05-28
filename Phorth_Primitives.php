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

	class PhorthEngine__Nop extends PhorthEngine___Word
	{
	}

	abstract class PhorthEngine___AbstractUnaryOp extends PhorthEngine___Word
	{
		protected $operator;

		protected function decide()
		{
			$this->pushDatum(call_user_func(
					create_function('$a', 'return '.$this->operator.' $a;'),
					$this->popDatum()));

			return $this;
		}
	}

	abstract class PhorthEngine___AbstractBinaryOp extends PhorthEngine___Word
	{
		protected $operator;

		protected function decide()
		{
			$this->pushDatum(call_user_func_array(
					create_function('$a, $b', 'return $a '.$this->operator.' $b;'),
					$this->popData(2)));

			return $this;
		}
	}

	abstract class PhorthEngine___AbstractNativeCall extends PhorthEngine___Word
	{
		protected $func;

		protected $numArgs;

		protected function decide()
		{
			if ($this->numArgs)
			{
				$args = array();

				for ($i = 0; $i != $this->numArgs; ++$i)
				{
					$args[] = '$a'.$i;
				}

				$argList = implode(', ', $args);

				$this->pushDatum(call_user_func_array(
						create_function($argList, 'return '.$this->func.'('.$argList.');'),
						$this->popData($this->numArgs)));
			}
			else
			{
				$this->pushDatum(call_user_func_array(
						create_function('', 'return '.$this->func.'();'),
						array()));
			}

			return $this;
		}
	}

	class PhorthEngine__UnaryOp extends PhorthEngine___AbstractUnaryOp
	{
		protected function decide()
		{
			$this->operator = $this->popDatum();

			return parent::decide();
		}
	}

	class PhorthEngine__BinaryOp extends PhorthEngine___AbstractBinaryOp
	{
		protected function decide()
		{
			$this->operator = $this->popDatum();

			return parent::decide();
		}
	}

	class PhorthEngine__NativeCall extends PhorthEngine___AbstractNativeCall
	{
		protected function decide()
		{
			list($this->numArgs, $this->func) = $this->popData(2);

			return parent::decide();
		}
	}

?>
