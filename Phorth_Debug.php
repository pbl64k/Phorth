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

	class PhorthEngine___DebugDumper extends PhorthEngine
	{
		static public function dumpState($engine)
		{
			$result = '';
			
			$result .= '<table border="1">';

			$selectedStacks = $engine->getSelectedStackIds();

			$result .= '<tr><td>state: '.$engine->getState().'</td></tr>';
			$result .= '<tr><td>context: '.$engine->getSelectedContextHandle().'</td></tr>';
			$result .= '<tr><td>global stack: '.
					$selectedStacks[$engine->getGlobalContextHandle()].'</td></tr>';
			$result .= '<tr><td>local stack: '.
					$selectedStacks[$engine->getLocalContextHandle()].'</td></tr>';

			$result .= '<tr><td>global stacks</td></tr>';

			foreach ($engine->getStacks($engine->getGlobalContextHandle()) as $id => $stack)
			{
				$result .= '<tr><td>'.$id.'</td>';

				foreach ($stack as $x)
				{
					$result .= '<td>'.$x.'</td>';
				}

				$result .= '</tr>';
			}

			$result .= '<tr><td>local stacks</td></tr>';

			foreach ($engine->getStacks($engine->getLocalContextHandle()) as $id => $stack)
			{
				$result .= '<tr><td>'.$id.'</td>';

				foreach ($stack as $x)
				{
					$result .= '<td>'.$x.'</td>';
				}

				$result .= '</tr>';
			}

			$result .= '</table>';

			return $result;
		}

		public function __construct()
		{
			die();
		}
	}

?>
