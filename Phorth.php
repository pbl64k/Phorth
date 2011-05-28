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

	require_once(dirname(__FILE__).'/Phorth_Stacks.php');
	require_once(dirname(__FILE__).'/Phorth_Engine.php');

	require_once(dirname(__FILE__).'/Phorth_Alias.php');
	require_once(dirname(__FILE__).'/Phorth_Errors.php');
	require_once(dirname(__FILE__).'/Phorth_Primitives.php');
	require_once(dirname(__FILE__).'/Phorth_Termination.php');
	require_once(dirname(__FILE__).'/Phorth_Sub.php');
	require_once(dirname(__FILE__).'/Phorth_Func.php');
	require_once(dirname(__FILE__).'/Phorth_Promise.php');
	require_once(dirname(__FILE__).'/Phorth_Context.php');
	require_once(dirname(__FILE__).'/Phorth_StackManip.php');
	require_once(dirname(__FILE__).'/Phorth_StackPrimitives.php');
	require_once(dirname(__FILE__).'/Phorth_Math.php');
	require_once(dirname(__FILE__).'/Phorth_String.php');
	require_once(dirname(__FILE__).'/Phorth_MapReduce.php');
	require_once(dirname(__FILE__).'/Phorth_CtrlPrimitives.php');
	require_once(dirname(__FILE__).'/Phorth_AdvCtrl.php');

	require_once(dirname(__FILE__).'/Phorth_Lib_Math.php');

	require_once(dirname(__FILE__).'/Phorth_Io.php');

	require_once(dirname(__FILE__).'/Phorth_Debug.php');

	require_once(dirname(__FILE__).'/Phorth_Lib_Examples.php');

	class Phorth
	{
		static public function runWith($oracle)
		{
			return PhorthEngine::create($oracle);
		}

		static public function run()
		{
			return Phorth::runWith(NULL);
		}
	}

?>
