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

	class PhorthEngine__Repeat extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($f, $n) = $this->popData(2);

			$st = $this;

			while ($n--)
			{
				$st = $f->invokeWith($st);
			}

			return $st;
		}
	}

	class PhorthEngine__XRepeat extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->X__Repeat;
		}
	}

	class PhorthEngine__DotDot extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($n, $m) = $this->popData(2);

			$diff = $m - $n;

			$sgn = ($diff > 0) ? 1 : -1;

			for ($i = $n; (($m - $i) * $sgn) >= 0; $i += $sgn)
			{
				$this->pushDatum($i);
			}

			return $this;
		}
	}

	class PhorthEngine__Choose extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($then, $else, $condition) = $this->popData(3);

			$this->pushDatum($condition ? $then : $else);

			return $this->CallIfApplicable;
		}
	}

	class PhorthEngine__OnlyIf extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->_Nop->X__Choose;
		}
	}

?>
