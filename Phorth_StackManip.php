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

	class PhorthEngine__CreateStack extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum($this->createStack());

			return $this;
		}
	}

	class PhorthEngine__DestroyStack extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->killStack($this->popDatum());

			return $this;
		}
	}

	class PhorthEngine__Selected extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum($this->getSelectedStackId());

			return $this;
		}
	}

	class PhorthEngine__Select extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->selectStack($this->popDatum());

			return $this;
		}
	}

	class PhorthEngine__SelectResult extends PhorthEngine___Alias
	{
		protected function decide()
		{
			$this->selectStack($this->getResultStackId());

			return $this;
		}
	}

	class PhorthEngine__SelectData extends PhorthEngine___Alias
	{
		protected function decide()
		{
			$this->selectStack($this->getDataStackId());

			return $this;
		}
	}

	class PhorthEngine__SelectHeap extends PhorthEngine___Alias
	{
		protected function decide()
		{
			$this->selectStack($this->getHeapStackId());

			return $this;
		}
	}

	class PhorthEngine__RememberStack extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->pushDatum($this->getSelectedStackId(), '__stack_history');

			return $this;
		}
	}

	class PhorthEngine__RecallStack extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->selectStack($this->popDatum('__stack_history'));

			return $this;
		}
	}

	class PhorthEngine__SWipe extends PhorthEngine___Word
	{
		protected function decide()
		{
			$this->setStack(array(), $this->popDatum());

			return $this;
		}
	}

	class PhorthEngine__Init extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->SWipe;
		}
	}

	class PhorthEngine__Wipe extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->SWipe(NULL);
		}
	}

	class PhorthEngine__MovTo extends PhorthEngine___Word
	{
		protected function decide()
		{
			list($x, $stack) = $this->popData(2);

			$this->pushDatum($x, $stack);

			return $this;
		}
	}

	class PhorthEngine__MovFrom extends PhorthEngine___Word
	{
		protected function decide()
		{
			$stack = $this->popDatum();

			$this->pushDatum($this->popDatum($stack));

			return $this;
		}
	}

	class PhorthEngine__CopyTo extends PhorthEngine___Word
	{
		protected function decide()
		{
			$stack = $this->popDatum();

			$this->pushDatum($this->peekDatum(), $stack);

			return $this;
		}
	}

	class PhorthEngine__CopyFrom extends PhorthEngine___Word
	{
		protected function decide()
		{
			$stack = $this->popDatum();

			$this->pushDatum($this->peekDatum($stack));

			return $this;
		}
	}

	class PhorthEngine__MovToResult extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->MovTo($this->getResultStackId());
		}
	}

	class PhorthEngine__MovFromResult extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->MovFrom($this->getResultStackId());
		}
	}

	class PhorthEngine__CopyToResult extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->CopyTo($this->getResultStackId());
		}
	}

	class PhorthEngine__CopyFromResult extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->CopyFrom($this->getResultStackId());
		}
	}

	class PhorthEngine__MovToData extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->MovTo($this->getDataStackId());
		}
	}

	class PhorthEngine__MovFromData extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->MovFrom($this->getDataStackId());
		}
	}

	class PhorthEngine__CopyToData extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->CopyTo($this->getDataStackId());
		}
	}

	class PhorthEngine__CopyFromData extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->CopyFrom($this->getDataStackId());
		}
	}

	class PhorthEngine__MovToHeap extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->MovTo($this->getHeapStackId());
		}
	}

	class PhorthEngine__MovFromHeap extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->MovFrom($this->getHeapStackId());
		}
	}

	class PhorthEngine__CopyToHeap extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->CopyTo($this->getHeapStackId());
		}
	}

	class PhorthEngine__CopyFromHeap extends PhorthEngine___Alias
	{
		protected function decide()
		{
			return $this->CopyFrom($this->getHeapStackId());
		}
	}

?>
