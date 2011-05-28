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

	class PhorthEngine__SwitchContext extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->selectContext($this->popDatum());

			return $this;
		}
	}

	class PhorthEngine__Context extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum($this->getSelectedContextHandle());

			return $this;
		}
	}

	class PhorthEngine__SwitchToGlobal extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->selectContext($this->getGlobalContextHandle());

			return $this;
		}
	}

	class PhorthEngine__SwitchToLocal extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->selectContext($this->getLocalContextHandle());

			return $this;
		}
	}

?>
