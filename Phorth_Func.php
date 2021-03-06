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

	abstract class PhorthEngine___Func extends PhorthEngine___Sub
	{
		public function decidePostwrapper()
		{
			$this->selectStacks(array(
					$this->getGlobalContextHandle() => $this->getResultStackId(),
					$this->getLocalContextHandle() => $this->getHeapStackId(),
					));
			$this->selectContext($this->getLocalContextHandle());

			return parent::decidePostwrapper();
		}
	}

?>
