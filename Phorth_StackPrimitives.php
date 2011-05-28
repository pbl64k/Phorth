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

	class PhorthEngine__NumOf extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum($this->getStackElementCount());

			return $this;
		}
	}

	class PhorthEngine__IsEmpty extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum($this->isStackEmpty());

			return $this;
		}
	}

	class PhorthEngine__Push extends PhorthEngine__Nop
	{
	}

	class PhorthEngine__Null extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum(NULL);

			return $this;
		}
	}

	class PhorthEngine__Drop extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->popDatum();

			return $this;
		}
	}

	class PhorthEngine__Nip extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($next, $top) = $this->popData(2);

			$this->pushDatum($top);

			return $this;
		}
	}

	class PhorthEngine__Dup extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum($this->peekDatum());

			return $this;
		}
	}

	class PhorthEngine__Swap extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($next, $top) = $this->popData(2);

			$this->pushData(array($top, $next));

			return $this;
		}
	}

	class PhorthEngine__Over extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($next, $top) = $this->popData(2);

			$this->pushData(array($next, $top, $next));

			return $this;
		}
	}

	class PhorthEngine__Tuck extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($next, $top) = $this->popData(2);

			$this->pushData(array($top, $next, $top));

			return $this;
		}
	}

	class PhorthEngine__Rot extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($lowest, $next, $top) = $this->popData(3);

			$this->pushData(array($next, $top, $lowest));

			return $this;
		}
	}

	class PhorthEngine__Unrot extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($lowest, $next, $top) = $this->popData(3);

			$this->pushData(array($top, $lowest, $next));

			return $this;
		}
	}

	class PhorthEngine__DDrop extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->popData(2);

			return $this;
		}
	}

	class PhorthEngine__DNip extends PhorthEngine___Word
	{
		protected function decide()
		{
			$xs = $this->popData(4);

			$this->pushData(array($xs[2], $xs[3]));

			return $this;
		}
	}

	class PhorthEngine__DDup extends PhorthEngine___Word
	{
		protected function decide()
		{
			$xs = $this->popData(2);

			$this->pushData($xs);
			$this->pushData($xs);

			return $this;
		}
	}

	class PhorthEngine__DSwap extends PhorthEngine___Word
	{
		protected function decide()
		{
			$xs = $this->popData(4);

			$this->pushData(array($xs[2], $xs[3], $xs[0], $xs[1]));

			return $this;
		}
	}

	class PhorthEngine__DOver extends PhorthEngine___Word
	{
		protected function decide()
		{
			$xs = $this->popData(4);

			$this->pushData($xs);
			$this->pushData(array($xs[0], $xs[1]));

			return $this;
		}
	}

	class PhorthEngine__DTuck extends PhorthEngine___Word
	{
		protected function decide()
		{
			$xs = $this->popData(4);

			$this->pushData(array($xs[2], $xs[3]));
			$this->pushData($xs);

			return $this;
		}
	}

	class PhorthEngine__DRot extends PhorthEngine___Word
	{
		protected function decide()
		{
			$xs = $this->popData(6);

			$this->pushData(array($xs[2], $xs[3], $xs[4], $xs[5], $xs[0], $xs[1]));

			return $this;
		}
	}

	class PhorthEngine__DUnrot extends PhorthEngine___Word
	{
		protected function decide()
		{
			$xs = $this->popData(6);

			$this->pushData(array($xs[4], $xs[5], $xs[0], $xs[1], $xs[2], $xs[3]));

			return $this;
		}
	}

	class PhorthEngine__PDup extends PhorthEngine___Word
	{
		protected function decide()
		{
			$top = $this->peekData();

			if ($top)
			{
				$this->pushData($top);
			}

			return $this;
		}
	}

	class PhorthEngine__Pick extends PhorthEngine___Word
	{
		protected function decide()
		{
			$n = $this->popDatum();

			$x = $this->popData($n + 1);

			$this->pushData($x);

			$this->pushDatum($x[0]);

			return $this;
		}
	}

	class PhorthEngine__Snip extends PhorthEngine___Word
	{
		protected function decide()
		{
			$n = $this->popDatum();

			$x = $this->popData($n);

			$this->popDatum();

			$this->pushData($x);

			return $this;
		}
	}

	class PhorthEngine__Roll extends PhorthEngine___Word
	{
		protected function decide()
		{
			$n = $this->popDatum();

			$x = $this->popData($n);

			$t = $this->popDatum();

			$this->pushData($x);

			$this->pushDatum($t);

			return $this;
		}
	}

	class PhorthEngine__Unroll extends PhorthEngine___Word
	{
		protected function decide()
		{
			$n = $this->popDatum();

			$t = $this->popDatum();

			$x = $this->popData($n);

			$this->pushDatum($t);

			$this->pushData($x);

			return $this;
		}
	}

?>
