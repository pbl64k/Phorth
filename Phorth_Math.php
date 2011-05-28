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

	class PhorthEngine__BNot extends PhorthEngine___AbstractUnaryOp
	{
		protected $operator = '!';
	}

	class PhorthEngine__BAnd extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '&&';
	}

	class PhorthEngine__BOr extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '||';
	}

	class PhorthEngine__AInc extends PhorthEngine___AbstractUnaryOp
	{
		protected $operator = '++';
	}

	class PhorthEngine__ADec extends PhorthEngine___AbstractUnaryOp
	{
		protected $operator = '--';
	}

	class PhorthEngine__AAdd extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '+';
	}

	class PhorthEngine__ASub extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '-';
	}

	class PhorthEngine__AMul extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '*';
	}

	class PhorthEngine__ADiv extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '/';

		protected function decide()
		{
			if (! $this->peekDatum())
			{
				list($denom, $divis) = $this->popData(2);

				throw new PhorthEngine___DivisionByZeroException($denom);
			}

			return parent::decide();
		}
	}

	class PhorthEngine__AMod extends PhorthEngine__ADiv
	{
		protected $operator = '%';
	}

	class PhorthEngine__CEq extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '==';
	}

	class PhorthEngine__CNe extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '!=';
	}

	class PhorthEngine__CLe extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '<=';
	}

	class PhorthEngine__CLt extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '<';
	}

	class PhorthEngine__CGe extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '>=';
	}

	class PhorthEngine__CGt extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '>';
	}

	class PhorthEngine__CTEq extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '===';
	}

	class PhorthEngine__CTNe extends PhorthEngine___AbstractBinaryOp
	{
		protected $operator = '!==';
	}

	class PhorthEngine__CBetween extends PhorthEngine___Word
	{
		protected function decide()
		{
			$args = $this->popData(3);
			$x = array_shift($args);
			sort($args);

			$this->pushDatum(($x >= $args[0]) && ($x <= $args[1]));

			return $this;
		}
	}

	class PhorthEngine__CBetweenX extends PhorthEngine___Word
	{
		protected function decide()
		{
			$args = $this->popData(3);
			$x = array_shift($args);
			sort($args);

			$this->pushDatum(($x > $args[0]) && ($x < $args[1]));

			return $this;
		}
	}

	class PhorthEngine__NASum extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->_AAdd->XNReduce;
		}
	}

	class PhorthEngine__ASum extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->NASum(NULL);
		}
	}

	class PhorthEngine__NAProd extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->_AMul->XNReduce;
		}
	}

	class PhorthEngine__AProd extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->NAProd(NULL);
		}
	}

	class PhorthEngine__NReduceBAnd extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->_BAnd->XNReduce;
		}
	}

	class PhorthEngine__ReduceBAnd extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->NReduceBAnd(NULL);
		}
	}

	class PhorthEngine__NReduceBOr extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->_BOr->XNReduce;
		}
	}

	class PhorthEngine__ReduceBOr extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->NReduceBOr(NULL);
		}
	}

	class PhorthEngine__MAbs extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum(abs($this->popDatum()));

			return $this;
		}
	}

	class PhorthEngine__MSgn extends PhorthEngine___Word
	{
		protected function decide()
		{
			$x = $this->popDatum();

			$this->pushDatum(($x > 0) ? 1 : (($x < 0) ? -1 : 0));

			return $this;
		}
	}

	class PhorthEngine__MMin2 extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($x, $y) = $this->popData(2);

			$this->pushDatum(min($x, $y));

			return $this;
		}
	}

	class PhorthEngine__MMax2 extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($x, $y) = $this->popData(2);

			$this->pushDatum(max($x, $y));

			return $this;
		}
	}

	class PhorthEngine__NMMin extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->_MMin2->XNReduce;
		}
	}

	class PhorthEngine__NMMax extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->_MMax2->XNReduce;
		}
	}

	class PhorthEngine__MMin extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->NMMin(NULL);
		}
	}

	class PhorthEngine__MMax extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->NMMax(NULL);
		}
	}

?>
