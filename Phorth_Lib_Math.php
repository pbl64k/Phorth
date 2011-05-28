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

	class PhorthEngine__NMAvg extends PhorthEngine___Func
	{
		protected function decide()
		{
			return $this
			->	SelectResult
			->	CopyToHeap
			->	NASum
			->	MovFromHeap
			->	ADiv
			;
		}
	}

	class PhorthEngine__MAvg extends PhorthEngine___Func
	{
		protected function decide()
		{
			return $this
			->	SelectResult
			->	NumOf
			->	NMAvg
			;
		}
	}

	class PhorthEngine__MFactorial extends PhorthEngine___Func
	{
		protected function decide()
		{
			return $this
			->	SelectResult
			->	X__MFactorialHelper(1)
			;
		}
	}

	class PhorthEngine__MFactorialHelper extends PhorthEngine___Func
	{
		protected function decide()
		{
//print(memory_get_usage().'<br>');
			return $this
			->	SelectResult
			->	Dup
			->	CLe(1)
			->	IfThen
			->		D__Yield
			->	Otherwise
			->		WU__AMul
			->		Swap
			->		ASub(1)
			->		Tail
			//->		MFactorialHelper
			->	NehtFi
			;
		}
	}

	class PhorthEngine__MFibonacci extends PhorthEngine___Func
	{
		protected function decide()
		{
			return $this
			->	SelectResult
			->	Rot(1, 1)
			->	MFibonacciHelper
			->	Nip
			;
		}
	}

	class PhorthEngine__MFibonacciHelper extends PhorthEngine___Func
	{
		protected function decide()
		{
//print(memory_get_usage().'<br>');
//print_r($this->getStack('__result'));print('<br>');
			return $this
			->	SelectResult
			->	CopyToHeap
			->	CLe(2)
			->	IfThen
			->		Yield
			->	Otherwise
			->		Tuck
			->		AAdd
			->		MovFromHeap
			->		ASub(1)
			->		Tail
			//->		MFibonacciHelper
			->	NehtFi
			;
		}
	}

?>
