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

	class PhorthEngine__NReduce extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($f, $n) = $this->popData(2);

			$params = array_reverse($this->popData($n));

			$init = array_shift($params);

//ob_end_flush();print($f->getInvocationString());die();
//ob_end_flush();print_r($f);die();
			$this->pushDatum(array_reduce($params,
					create_function('$a, $b',
					'return PhorthEngine::create()->Push($a, $b)->'.
					$f->getInvocationString().'->getResult();'),
					$init));

			return $this;
		}
	}

	class PhorthEngine__XNReduce extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->X__NReduce;
		}
	}

	class PhorthEngine__Reduce extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->NReduce(NULL);
		}
	}

	class PhorthEngine__NMap extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($f, $n) = $this->popData(2);

			$params = $this->popData($n);

			$this->pushData(array_reverse(array_map(
					create_function('$a',
					'return PhorthEngine::create()->Push($a)->'.
					$f->getInvocationString().'->getResult();'),
					array_reverse($params))));

			return $this;
		}
	}

	class PhorthEngine__XNMap extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->X__NMap;
		}
	}

	class PhorthEngine__Map extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->NMap(NULL);
		}
	}

	class PhorthEngine__NZipWith extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($f, $a, $n) = $this->popData(3);

			$params = $this->popData($n);
			$params2 = $this->popData(count($params), $a);

			$fx = create_function('$a, $b',
					'return PhorthEngine::create()->Push($a, $b)->'.
					$f->getInvocationString().'->getResult();');

			$results = array();

			foreach ($params as $i => $param)
			{
				$results[$i] = call_user_func($fx, $param, $params2[$i]);
			}

			$this->pushData($results);

			return $this;
		}
	}

	class PhorthEngine__XNZipWith extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->X__NZipWith;
		}
	}

	class PhorthEngine__ZipWith extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->NZipWith(NULL);
		}
	}

?>
