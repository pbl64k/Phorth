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

	abstract class PhorthEngine___StackManager
	{
		private $selectedContextHandle;

		private $selectedStackIds;

		private $stacks;

		protected function injectStacks($state)
		{
			$this->setStacks($state->getStacks());
			$this->selectStacks($state->getSelectedStackIds());
			$this->selectContext($state->getSelectedContextHandle());
		}

		protected function setStacks(&$stacks, $contextHandle = NULL)
		{
			if ($contextHandle)
			{
				$this->stacks[$contextHandle] = $stacks;
			}
			else
			{
				$this->stacks =& $stacks;
			}
		}

		protected function &getStacks($contextHandle = NULL)
		{
			if ($contextHandle)
			{
//if ($contextHandle == '__local') {print('[');print_r($this->stacks);print(']<br>');}
				return $this->stacks[$contextHandle];
			}

			return $this->stacks;
		}

		protected function getGlobalContextHandle()
		{
			return '__global';
		}

		protected function getLocalContextHandle()
		{
			return '__local';
		}

		protected function getResultStackId()
		{
			return '__result';
		}

		protected function getDataStackId()
		{
			return '__data';
		}

		protected function getHeapStackId()
		{
			return '__heap';
		}

		protected function isValidContext($contextHandle)
		{
			return in_array($contextHandle, array($this->getGlobalContextHandle(), $this->getLocalContextHandle()));
		}

		protected function selectContext($contextHandle)
		{
			if (! $this->isValidContext($contextHandle))
			{
				throw new PhorthEngine___InvalidContextException($contextHandle);
			}

			$this->selectedContextHandle = $contextHandle;
		}

		protected function getSelectedContextHandle($override = NULL)
		{
			if ($override)
			{
				$result = $override;
			}
			else
			{
				$result = $this->selectedContextHandle;
			}

			if (! $this->isValidContext($result))
			{
				throw new PhorthEngine___InvalidContextException($result);
			}

			return $result;
		}

		protected function selectStacks($stackIds)
		{
			$this->selectedStackIds = $stackIds;
		}

		protected function getSelectedStackIds()
		{
			return $this->selectedStackIds;
		}

		protected function getContextHandleForStackId($stackId)
		{
			if (in_array($this->getSelectedStackId($stackId),
					array($this->getResultStackId(), $this->getDataStackId(),
					'__sub_scope', '__tail_calls')))
			{
				return $this->getGlobalContextHandle();
			}
			
			if (in_array($this->getSelectedStackId($stackId),
					array($this->getHeapStackId(), '__state_scope', '__stack_history')))
			{
				return $this->getLocalContextHandle();
			}

			return $this->getSelectedContextHandle();
		}

		protected function existsStack($stackId)
		{
			return array_key_exists($stackId, $this->stacks[$this->getContextHandleForStackId($stackId)]);
		}

		protected function selectStack($stackId)
		{
			$stackId = $this->getSelectedStackId($stackId);

			if (! $this->existsStack($stackId))
			{
				$this->setStack(array(), $stackId);
			}

			$this->selectedStackIds[$this->getSelectedContextHandle()] = $stackId;
		}

		protected function getSelectedStackId($override = NULL)
		{
			if ($override)
			{
				return $override;
			}

			return $this->selectedStackIds[$this->getSelectedContextHandle()];
		}

		protected function createStack()
		{
			$idChars = '_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVXYZ';

			$stackId = '__dyn_0';

			while (isset($this->stacks[$this->getContextHandleForStackId($stackId)][$this->getSelectedStackId($stackId)]))
			{
				$stackId .= $idChars[rand(0, strlen($idChars) - 1)];
			}

			$this->setStack(array(), $stackId);

			return $stackId;
		}

		protected function killStack($stackId)
		{
			unset($this->stacks[$this->getContextHandleForStackId($stackId)][$this->getSelectedStackId($stackId)]);
		}

		protected function setStack($stack = array(), $stackId = NULL)
		{
			$this->stacks[$this->getContextHandleForStackId($stackId)]
					[$this->getSelectedStackId($stackId)] = $stack;
		}

		protected function getStack($stackId = NULL)
		{
			return $this->stacks[$this->getContextHandleForStackId($stackId)]
					[$this->getSelectedStackId($stackId)];
		}

		public function getStackElementCount($stackId = NULL)
		{
			return count($this->stacks[$this->getContextHandleForStackId($stackId)]
					[$this->getSelectedStackId($stackId)]);
		}

		public function isStackEmpty($stackId = NULL)
		{
			return (! $this->getStackElementCount($stackId));
		}

		public function pushDatum($datum, $stackId = NULL)
		{
//print('['.$datum.'] ['.$stackId.']'."\n");
			$this->stacks[$this->getContextHandleForStackId($stackId)]
					[$this->getSelectedStackId($stackId)][] = $datum;
//print('{'.$this->getContextHandleForStackId($stackId).'} ['.$this->getSelectedStackId($stackId).']'."\n");
//print('((('.print_r($this->stacks[$this->getContextHandleForStackId($stackId)]
//					[$this->getSelectedStackId($stackId)], TRUE).')))'."\n");

		}

		public function pushData($data, $stackId = NULL)
		{
			foreach ($data as $datum)
			{
				$this->pushDatum($datum, $stackId);
			}
		}

		public function popDatum($stackId = NULL)
		{
			if ($this->isStackEmpty($stackId))
			{
				throw new PhorthEngine___StackUnderflowException(
						$this->getSelectedStackId($stackId),
						'stack empty');
			}

			return array_pop($this->stacks[$this->getContextHandleForStackId($stackId)]
					[$this->getSelectedStackId($stackId)]);
		}

		public function popAllData($stackId = NULL)
		{
			$result = $this->getStack($stackId);

			$this->setStack(array(), $stackId);

			return $result;
		}

		public function popData($n = NULL, $stackId = NULL)
		{
			if (! $n)
			{
				return $this->popAllData($stackId);
			}

			if ($this->getStackElementCount($stackId) < $n)
			{
				throw new PhorthEngine___StackUnderflowException(
						$this->getSelectedStackId($stackId),
						'['.$this->getStackElementCount($stackId).
						'] elements in stack: requested ['.$n.']');
			}
			$data = array();

			while ($n--)
			{
				array_unshift($data, $this->popDatum($stackId));
			}

			return $data;
		}

		public function peekDatum($stackId = NULL)
		{
			if ($this->isStackEmpty($stackId))
			{
				throw new PhorthEngine___StackUnderflowException(
						$this->getSelectedStackId($stackId),
						'stack empty');
			}

			return $this->stacks[$this->getContextHandleForStackId($stackId)]
					[$this->getSelectedStackId($stackId)]
					[$this->getStackElementCount($stackId) - 1];
		}

		public function getResultStack()
		{
			return $this->getStack($this->getResultStackId());
		}

		public function getResult()
		{
			if ($this->isStackEmpty($this->getResultStackId()))
			{
				return NULL;
			}

			return $this->peekDatum($this->getResultStackId());
		}
	}

?>
