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

	$started = microtime(TRUE);

	error_reporting(E_ALL | E_STRICT);

	if (isset($_SERVER['HTTP_HOST']))
	{
		$lnbr = '<br>';
	}
	else
	{
		$lnbr = "\n";
	}

	ini_set('memory_limit', '256M');
	ini_set('max_execution_time', '120');

	assert_options(ASSERT_BAIL, TRUE);

	define('DEFAULT_NUM_OF_TESTS', 100);

	require_once(dirname(__FILE__).'/Phorth.php');

	function genRandBoolean()
	{
		return rand(0, 1);
	}

	function genRandInteger($min = 0)
	{
		return rand($min, getrandmax());
	}

	function genRandTinyInteger()
	{
		return rand(1, 9);
	}

	function genRandSmallInteger()
	{
		return rand(1, 99);
	}

	function genRandSmallishInteger()
	{
		return rand(1, 999);
	}

	function genRandReasonablyModestInteger()
	{
		return rand(1, 99999);
	}

	function genRandFloat()
	{
		return genRandInteger() / genRandInteger(1);
	}

	function testUnaryFunction($implementation, $knownGood,
			$argGen = 'genRandFloat', $attempts = DEFAULT_NUM_OF_TESTS)
	{
		global $lnbr;

		$totalAttempts = $attempts;

		print($implementation.': testing');

		$totalTime = 0;

		while ($attempts--)
		{
			$arg = $argGen();

			$started = microtime(TRUE);

			$imp = $implementation($arg);

			$finished = microtime(TRUE);

			$totalTime += ($finished - $started);

			$kg = $knownGood($arg);

			if (! ($imp == $kg))
			{
				print($lnbr.'TEST FAILED: (with: {'.print_r($arg, TRUE).'}) got {'.
						print_r($imp, TRUE).'}, expected {'.print_r($kg, TRUE).'}'.$lnbr);

				return FALSE;
			}

			print('.');
		}

		print('passed in '.round($totalTime, 4).' seconds, '.round($totalTime / $totalAttempts, 4).' seconds per test'.$lnbr);

		return TRUE;
	}

	function testUnaryOp($implementation, $knownGood,
			$arg = 'genRandFloat', $attempts = DEFAULT_NUM_OF_TESTS)
	{
		return testUnaryFunction($implementation,
				create_function('$a', 'return '.$knownGood.' $a;'), $arg, $attempts);
	}

	function testBinaryFunction($implementation, $knownGood,
			$firstArgGen = 'genRandFloat', $secondArgGen = 'genRandFloat',
			$attempts = DEFAULT_NUM_OF_TESTS)
	{
		global $lnbr;

		$totalAttempts = $attempts;

		print($implementation.': testing');

		$totalTime = 0;

		while ($attempts--)
		{
			$arg1 = $firstArgGen();
			$arg2 = $secondArgGen();

			$started = microtime(TRUE);

			$imp = $implementation($arg1, $arg2);

			$finished = microtime(TRUE);

			$totalTime += ($finished - $started);

			$kg = $knownGood($arg1, $arg2);

			if (! ($imp === $kg))
			{
				print($lnbr.'TEST FAILED: (with: {'.print_r($arg1, TRUE).'}, {'.
						print_r($arg2, TRUE).'}) got {'.print_r($imp, TRUE).'}, expected {'.
						print_r($kg, TRUE).'}'.$lnbr);

				return FALSE;
			}

			print('.');
		}

		print('passed in '.round($totalTime, 4).' seconds, '.round($totalTime / $totalAttempts, 4).' seconds per test'.$lnbr);

		return TRUE;
	}

	function testBinaryOp($implementation, $knownGood,
			$arg1 = 'genRandFloat', $arg2 = 'genRandFloat', $attempts = DEFAULT_NUM_OF_TESTS)
	{
		return testBinaryFunction($implementation,
				create_function('$a, $b', 'return $a '.$knownGood.' $b;'), $arg1, $arg2, $attempts);
	}

	function testDrop($implementation, $arg = 'genRandInteger',
			$attempts = DEFAULT_NUM_OF_TESTS)
	{
		return testBinaryFunction($implementation,
				create_function('$a, $b', 'return $a;'), $arg, $arg, $attempts);
	}

	function testStack($implementation, $expected, $argNum,
			$argGen = 'genRandFloat', $attempts = DEFAULT_NUM_OF_TESTS)
	{
		global $lnbr;

		$totalAttempts = $attempts;

		print($implementation.': testing');

		$totalTime = 0;

		$argList = array();

		for ($i = 0; $i != $argNum; ++$i)
		{
			$argList[] = '$a'.($i + 1);
		}

		$f = create_function(implode(', ', $argList), 'return '.$expected.';');

		while ($attempts--)
		{
			$args = array();

			for ($i = 0; $i != $argNum; ++$i)
			{
				$args[] = $argGen();
			}

			$started = microtime(TRUE);

			$imp = call_user_func_array($implementation, $args);

			$finished = microtime(TRUE);

			$totalTime += ($finished - $started);

			$kg = call_user_func_array($f, $args);

			if (count(array_diff_assoc($imp, $kg)) || count(array_diff_assoc($kg, $imp)))
			{
				print($lnbr.'TEST FAILED: (with: {'.print_r($args, TRUE).'}) got {'.
						print_r($imp, TRUE).'}, expected {'.print_r($kg, TRUE).'}'.$lnbr);

				return FALSE;
			}

			print('.');
		}

		print('passed in '.round($totalTime, 4).' seconds, '.round($totalTime / $totalAttempts, 4).' seconds per test'.$lnbr);

		return TRUE;
	}

	function test_Phorth_Not($arg)
	{
		return Phorth::run()->BNot($arg)->getResult();
	}

	function test_Phorth_And($arg1, $arg2)
	{
		return Phorth::run()->BAnd($arg1, $arg2)->getResult();
	}

	function test_Phorth_Or($arg1, $arg2)
	{
		return Phorth::run()->BOr($arg1, $arg2)->getResult();
	}

	function test_Phorth_Inc($arg)
	{
		return Phorth::run()->AInc($arg)->getResult();
	}

	function test_Phorth_Dec($arg)
	{
		return Phorth::run()->ADec($arg)->getResult();
	}

	function test_Phorth_Add($arg1, $arg2)
	{
		return Phorth::run()->AAdd($arg1, $arg2)->getResult();
	}

	function test_Phorth_Sub($arg1, $arg2)
	{
		return Phorth::run()->ASub($arg1, $arg2)->getResult();
	}

	function test_Phorth_Mul($arg1, $arg2)
	{
		return Phorth::run()->AMul($arg1, $arg2)->getResult();
	}

	function test_Phorth_Div($arg1, $arg2)
	{
		return Phorth::run()->ADiv($arg1, $arg2)->getResult();
	}

	function test_Phorth_Mod($arg1, $arg2)
	{
		return Phorth::run()->AMod($arg1, $arg2)->getResult();
	}

	function test_Phorth_Eq($arg1, $arg2)
	{
		return Phorth::run()->CEq($arg1, $arg2)->getResult();
	}

	function test_Phorth_Ne($arg1, $arg2)
	{
		return Phorth::run()->CNe($arg1, $arg2)->getResult();
	}

	function test_Phorth_Gt($arg1, $arg2)
	{
		return Phorth::run()->CGt($arg1, $arg2)->getResult();
	}

	function test_Phorth_Ge($arg1, $arg2)
	{
		return Phorth::run()->CGe($arg1, $arg2)->getResult();
	}

	function test_Phorth_Lt($arg1, $arg2)
	{
		return Phorth::run()->CLt($arg1, $arg2)->getResult();
	}

	function test_Phorth_Le($arg1, $arg2)
	{
		return Phorth::run()->CLe($arg1, $arg2)->getResult();
	}

	function test_Phorth_Btw($arg1, $arg2, $arg3)
	{
		return Phorth::run()->CBetween($arg1, $arg2, $arg3)->getResultStack();
	}

	function test_Phorth_Btw2($arg1, $arg2, $arg3)
	{
		return Phorth::run()->CBetweenX($arg1, $arg2, $arg3)->getResultStack();
	}

	function test_Phorth_Abs($arg1)
	{
		return Phorth::run()->MAbs($arg1)->getResultStack();
	}

	function test_Phorth_Sgn($arg1)
	{
		return Phorth::run()->MSgn($arg1)->getResultStack();
	}

	function test_Phorth_Wipe($a1, $a2, $a3)
	{
		return Phorth::run()->Push($a1, $a2, $a3)->Wipe->getResultStack();
	}

	function test_Phorth_Min($a1, $a2, $a3, $a4, $a5)
	{
		return Phorth::run()->MMin($a1, $a2, $a3, $a4, $a5)->getResultStack();
	}

	function test_Phorth_Max($a1, $a2, $a3, $a4, $a5)
	{
		return Phorth::run()->MMax($a1, $a2, $a3, $a4, $a5)->getResultStack();
	}

	function test_Phorth_Push($arg)
	{
		return Phorth::run()->Push($arg)->getResult();
	}

	function test_Phorth_Push2($arg)
	{
		return Phorth::run()->Push($arg)->getResultStack();
	}

	function test_Phorth_Drop($arg1, $arg2)
	{
		return Phorth::run()->Push($arg1, $arg2)->Drop->getResult();
	}

	function test_Phorth_Drop2($arg1, $arg2)
	{
		return Phorth::run()->Push($arg1, $arg2)->Drop->getResultStack();
	}

	function test_Phorth_Nip($arg1, $arg2)
	{
		return Phorth::run()->Push($arg1, $arg2)->Nip->getResultStack();
	}

	function test_Phorth_Dup($arg)
	{
		return Phorth::run()->Dup($arg)->AAdd->getResult();
	}

	function test_Phorth_Dup2($arg)
	{
		return Phorth::run()->Dup($arg)->getResultStack();
	}

	function test_Phorth_Swap($arg1, $arg2)
	{
		return Phorth::run()->Swap($arg1, $arg2)->getResult();
	}

	function test_Phorth_Swap2($arg1, $arg2)
	{
		return Phorth::run()->Swap($arg1, $arg2)->getResultStack();
	}

	function test_Phorth_Over($arg1, $arg2)
	{
		return Phorth::run()->Push($arg1, $arg2)->Over->getResultStack();
	}

	function test_Phorth_Tuck($arg1, $arg2)
	{
		return Phorth::run()->Push($arg1, $arg2)->Tuck->getResultStack();
	}

	function test_Phorth_Rot($arg1, $arg2, $arg3)
	{
		return Phorth::run()->Rot($arg1, $arg2, $arg3)->getResultStack();
	}

	function test_Phorth_Unrot($arg1, $arg2, $arg3)
	{
		return Phorth::run()->Unrot($arg1, $arg2, $arg3)->getResultStack();
	}

	function test_Phorth_DDrop($arg1, $arg2, $arg3)
	{
		return Phorth::run()->DDrop($arg1, $arg2, $arg3)->getResultStack();
	}

	function test_Phorth_DNip($arg1, $arg2, $arg3, $arg4)
	{
		return Phorth::run()->DNip($arg1, $arg2, $arg3, $arg4)->getResultStack();
	}

	function test_Phorth_DDup($arg1, $arg2)
	{
		return Phorth::run()->DDup($arg1, $arg2)->getResultStack();
	}

	function test_Phorth_DOver($arg1, $arg2, $arg3, $arg4)
	{
		return Phorth::run()->DOver($arg1, $arg2, $arg3, $arg4)->getResultStack();
	}

	function test_Phorth_DTuck($arg1, $arg2, $arg3, $arg4)
	{
		return Phorth::run()->DTuck($arg1, $arg2, $arg3, $arg4)->getResultStack();
	}

	function test_Phorth_DSwap($arg1, $arg2, $arg3, $arg4)
	{
		return Phorth::run()->DSwap($arg1, $arg2, $arg3, $arg4)->getResultStack();
	}

	function test_Phorth_DRot($arg1, $arg2, $arg3, $arg4, $arg5, $arg6)
	{
		return Phorth::run()->DRot($arg1, $arg2, $arg3, $arg4, $arg5, $arg6)->getResultStack();
	}

	function test_Phorth_DUnrot($arg1, $arg2, $arg3, $arg4, $arg5, $arg6)
	{
		return Phorth::run()->DUnrot($arg1, $arg2, $arg3, $arg4, $arg5, $arg6)->getResultStack();
	}

	function test_Phorth_Pick($arg1, $arg2, $arg3, $arg4, $arg5)
	{
		return Phorth::run()->Push($arg1, $arg2, $arg3, $arg4, $arg5, 3)->Pick->getResultStack();
	}

	function test_Phorth_Snip($arg1, $arg2, $arg3, $arg4, $arg5)
	{
		return Phorth::run()->Push($arg1, $arg2, $arg3, $arg4, $arg5, 4)->Snip->getResultStack();
	}

	function test_Phorth_Roll($arg1, $arg2, $arg3, $arg4, $arg5)
	{
		return Phorth::run()->Push($arg1, $arg2, $arg3, $arg4, $arg5, 4)->Roll->getResultStack();
	}

	function test_Phorth_Unroll($arg1, $arg2, $arg3, $arg4, $arg5)
	{
		return Phorth::run()->Push($arg1, $arg2, $arg3, $arg4, $arg5, 4)->Unroll->getResultStack();
	}

	function test_Phorth_Manip($arg1, $arg2, $arg3, $arg4, $arg5)
	{
		return Phorth::run()->Push($arg1, $arg2, $arg3, $arg4, $arg5)->NORX__AAdd->getResultStack();
	}

	function test_Phorth_Manip2($arg1, $arg2, $arg3, $arg4, $arg5)
	{
		return Phorth::run()->Push($arg1, $arg2, $arg3, $arg4, $arg5)->TDUW__AMul->getResultStack();
	}

	function test_Phorth_Stacks($arg1, $arg2)
	{
		return Phorth::run()->Push($arg1, $arg2)->MovToHeap->CopyToHeap->
				SelectHeap->AAdd->SelectResult->MovFromHeap->getResultStack();
	}

	function test_Phorth_Stacks2($arg1, $arg2, $arg3, $arg4, $arg5, $arg6)
	{
		return Phorth::run()->SwitchToLocal->SelectResult->Push($arg1, $arg2, $arg3, $arg4, $arg5, $arg6)->
				CreateStack->RememberStack->Select->MovFromResult->
				CreateStack->RememberStack->Select->MovFromResult->
				CreateStack->RememberStack->Select->N2__MovFromResult->AAdd->CopyToData->
				RecallStack->MovFromResult->AMul->CopyToData->Selected->DestroyStack->
				RecallStack->CopyFromResult->AAdd->CopyToData->Selected->DestroyStack->
				SelectData->N3__MovToResult->SelectResult->DestroyStack('__data')->getResultStack();
	}

	function test_Phorth_Stacks3($a1, $a2, $a3, $a4)
	{
		return Phorth::run()->SwitchToGlobal->SelectResult->Push($a1, $a2, $a3, $a4)->
			Select('testStack')->N2__MovFromResult->
			SwitchToLocal->Select('testStack')->N2__MovFromResult->AMul->CopyToResult->
			SwitchToGlobal->AAdd->CopyToResult->
			SelectResult->ASub->getResultStack();
	}

	function test_Phorth_Reduce($arg1, $arg2, $arg3)
	{
		return array(Phorth::run()->Push($arg1, $arg2, $arg3)->_ADiv->Reduce->getResult());
	}

	function test_Phorth_Map($arg1, $arg2, $arg3)
	{
		return Phorth::run()->Push($arg1, $arg2, $arg3)->_BNot->Map->getResultStack();
	}

	function test_Phorth_ZipWith($a1, $a2, $a3, $a4, $a5, $a6)
	{
		return Phorth::run()->SelectData->Selected->MovToHeap->Push($a1, $a2, $a3, $a4, $a5, $a6)->
			SelectResult->N3__MovFromData->
			_AAdd->MovFromHeap->ZipWith->
			getResultStack();
	}

	function test_Phorth_Repeat($arg1, $arg2, $arg3, $arg4)
	{
		return Phorth::run()->Push($arg1, $arg2, $arg3, $arg4)->_AMul->Repeat(3)->getResultStack();
	}

	function test_Phorth_Repeat2($arg1, $arg2, $arg3, $arg4)
	{
		return Phorth::run()->Push($arg1, $arg2, $arg3, $arg4)->N3__AMul->getResultStack();
	}

	function test_Phorth_Sum($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10)
	{
		return Phorth::run()->ASum($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10)->getResultStack();
	}

	function test_Phorth_Prod($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10)
	{
		return Phorth::run()->AProd($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10)->getResultStack();
	}

	function test_Phorth_Choose($arg1, $arg2, $arg3, $arg4)
	{
		return Phorth::run()->Push($arg1, $arg2)->
				_AAdd->_ASub->CGt($arg3, $arg4)->Choose->getResultStack();
	}

	function test_Phorth_Choose2($arg1, $arg2, $arg3, $arg4)
	{
		return Phorth::run()->Push($arg1, $arg2)->CGt($arg3, $arg4)->Choose->getResultStack();
	}

	function test_Phorth_OnlyIf($arg1, $arg2, $arg3, $arg4)
	{
		return array(Phorth::run()->Push($arg1, $arg2)->CGt($arg3, $arg4)->OnlyIf->getResult());
	}

	function test_Phorth_Native($a1, $a2, $a3, $a4, $a5)
	{
		return Phorth::run()->Host5__min($a1, $a2, $a3, $a4, $a5)->getResultStack();
	}

	function test_Phorth_Bail($arg1, $arg2)
	{
		return Phorth::run()->Terminate($arg1)->Push($arg2)->getResult();
	}

	function test_Phorth_Bail2($arg1, $arg2, $arg3)
	{
		return Phorth::run()->Terminate($arg1)->Push($arg2, $arg3)->getResultStack();
	}

	class PhorthEngine__TestTermSub1 extends PhorthEngine___Sub
	{
		protected function decide()
		{
			return $this->Dup->Nip->TestTermSub2->AMul(2)->Push(666);
		}
	}

	class PhorthEngine__TestTermSub2 extends PhorthEngine___Sub
	{
		protected function decide()
		{
			return $this->AAdd(1)->Terminate->Drop->AMul(2);
		}
	}

	function test_Phorth_Bail3($arg1, $arg2, $arg3)
	{
		return Phorth::run()->TestTermSub1($arg1, $arg2)->Dup->Push($arg3)->Nop->getResultStack();
	}

	function test_Phorth_Avg($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10)
	{
		return Phorth::run()->MAvg($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10)->getResultStack();
	}

	class PhorthEngine__TestSub1 extends PhorthEngine___Sub
	{
		protected function decide()
		{
			return $this->Yield->Push(1)->AMul(2, 3);
		}
	}

	function test_Phorth_Yield($arg1)
	{
		return Phorth::run()->TestSub1(666, $arg1)->AAdd->getResultStack();
	}

	function fac($n)
	{
		$result = 1;

		for ($i = 1; $i <= $n; ++$i)
		{
			$result *= $i;
		}

		return $result;
	}

	class PhorthEngine__TestDotDotFac extends PhorthEngine___Sub
	{
		protected function decide()
		{
			return $this->X__DotDot(1)->AProd;
		}
	}

	function test_Phorth_Fac($a1)
	{
		return Phorth::run()->Wipe->TestDotDotFac($a1)->getResultStack();
	}

	function test_Phorth_Fac2($a1)
	{
		return Phorth::run()->MFactorial($a1)->getResultStack();
	}

	function fib($n, $a = 1, $b = 1)
	{
		if ($n < 3)
		{
			return $b;
		}

		return fib(--$n, $b, $a + $b);
	}

	class PhorthEngine__TestNaiveFib extends PhorthEngine___Sub
	{
		protected function decide()
		{
			return $this->Dup->_TestNaiveFibYieldAndCleanup(1)->Swap->CLt(3)->OnlyIf->
					ASub(1)->Dup->TestNaiveFib->Swap->ASub(1)->TestNaiveFib->AAdd;
		}
	}

	class PhorthEngine__TestNaiveFibYieldAndCleanup extends PhorthEngine
	{
		protected function decide()
		{
			return $this->Swap->Drop->Yield;
		}
	}

	function test_Phorth_NaiveFib($arg1)
	{
		return Phorth::run()->TestNaiveFib($arg1)->getResultStack();
	}

	class PhorthEngine__TestSlightlyLessNaiveFib extends PhorthEngine___Sub
	{
		protected function decide()
		{
			return $this->Push(1, 1)->Rot->TestIterFib;
		}
	}

	class PhorthEngine__TestIterFib extends PhorthEngine___Sub
	{
		protected function decide()
		{
			return $this->CopyToHeap->_Yield->Swap->CLe(2)->OnlyIf->
					Tuck->AAdd->MovFromHeap->ASub(1)->TestIterFib;
		}
	}

	function test_Phorth_NaiveFib2($arg1)
	{
		return Phorth::run()->TestSlightlyLessNaiveFib($arg1)->getResultStack();
	}

	function test_Phorth_Fib($a1)
	{
		return Phorth::run()->MFibonacci($a1)->getResultStack();
	}

	class PhorthEngine__TestScoping extends PhorthEngine___Sub
	{
		protected function decide()
		{
			return $this->SelectResult->Nop->MovToHeap->
					TestScopingSub1->TestScopingSub2->CopyFromHeap->AAdd;
		}
	}

	class PhorthEngine__TestScopingSub1 extends PhorthEngine___Sub
	{
		protected function decide()
		{
			return $this->SelectHeap->MovFromResult->AAdd(1)->CopyToResult->
					TestScopingSub2->CopyFromResult;
		}
	}

	class PhorthEngine__TestScopingSub2 extends PhorthEngine___Sub
	{
		protected function decide()
		{
			return $this->SelectHeap->MovFromResult->Dup->AAdd->CopyToResult;
		}
	}

	function test_Phorth_Scope($arg1, $arg2)
	{
		return Phorth::run()->SelectResult->TestScoping($arg1, $arg2)->getResultStack();
	}

	class PhorthEngine__TestScopingFunc1 extends PhorthEngine___Func
	{
		protected function decide()
		{
			return $this->MovFromResult->SelectResult->AAdd()->TestScopingFunc2->CopyFromHeap->AMul;
		}
	}

	class PhorthEngine__TestScopingFunc2 extends PhorthEngine___Func
	{
		protected function decide()
		{
			return $this->MovFromResult->W__AAdd->CopyToResult;
		}
	}

	function test_Phorth_FuncScope($a1, $a2, $a3)
	{
		return Phorth::run()->Push($a1, $a2, $a3)->TestScopingFunc1->getResultStack();
	}

	class PhorthEngine__TestIfThen extends PhorthEngine___Sub
	{
		protected function decide()
		{
			return $this->CGt->IfThen->AAdd->AMul(2)->Nop->Otherwise->ASub->ADiv(2)->BNot->NehtFi->Yield;
		}
	}

	function test_Phorth_IfThen($a1, $a2, $a3, $a4)
	{
		return Phorth::run()->Push($a1, $a2, $a3, $a4)->TestIfThen->getResultStack();
	}

	assert(testUnaryOp('test_Phorth_Not', '!', 'genRandBoolean'));
	assert(testBinaryOp('test_Phorth_And', '&&', 'genRandBoolean', 'genRandBoolean'));
	assert(testBinaryOp('test_Phorth_Or', '||', 'genRandBoolean', 'genRandBoolean'));

	assert(testUnaryOp('test_Phorth_Inc', '++', 'genRandReasonablyModestInteger'));
	assert(testUnaryOp('test_Phorth_Dec', '--', 'genRandReasonablyModestInteger'));
	assert(testBinaryOp('test_Phorth_Add', '+', 'genRandReasonablyModestInteger', 'genRandReasonablyModestInteger'));
	assert(testBinaryOp('test_Phorth_Sub', '-', 'genRandReasonablyModestInteger', 'genRandReasonablyModestInteger'));
	assert(testBinaryOp('test_Phorth_Mul', '*', 'genRandReasonablyModestInteger', 'genRandReasonablyModestInteger'));
	assert(testBinaryOp('test_Phorth_Div', '/', 'genRandReasonablyModestInteger', 'genRandReasonablyModestInteger'));
	assert(testBinaryOp('test_Phorth_Mod', '%', 'genRandReasonablyModestInteger', 'genRandReasonablyModestInteger'));

	assert(testBinaryOp('test_Phorth_Eq', '==', 'genRandReasonablyModestInteger', 'genRandReasonablyModestInteger'));
	assert(testBinaryOp('test_Phorth_Ne', '!=', 'genRandReasonablyModestInteger', 'genRandReasonablyModestInteger'));
	assert(testBinaryOp('test_Phorth_Lt', '<', 'genRandReasonablyModestInteger', 'genRandReasonablyModestInteger'));
	assert(testBinaryOp('test_Phorth_Le', '<=', 'genRandReasonablyModestInteger', 'genRandReasonablyModestInteger'));
	assert(testBinaryOp('test_Phorth_Gt', '>', 'genRandReasonablyModestInteger', 'genRandReasonablyModestInteger'));
	assert(testBinaryOp('test_Phorth_Ge', '>=', 'genRandReasonablyModestInteger', 'genRandReasonablyModestInteger'));

	assert(testStack('test_Phorth_Btw', 'array(($a1 >= min($a2, $a3)) && ($a1 <= max($a2, $a3)))', 3, 'genRandReasonablyModestInteger'));
	assert(testStack('test_Phorth_Btw2', 'array(($a1 > min($a2, $a3)) && ($a1 < max($a2, $a3)))', 3, 'genRandReasonablyModestInteger'));

	assert(testStack('test_Phorth_Abs', 'array(abs($a1))', 1, 'genRandReasonablyModestInteger'));
	assert(testStack('test_Phorth_Sgn', 'array(abs($a1) / $a1)', 1, 'genRandReasonablyModestInteger'));
	assert(testStack('test_Phorth_Min', 'array(min($a1, $a2, $a3, $a4, $a5))', 5, 'genRandReasonablyModestInteger'));
	assert(testStack('test_Phorth_Max', 'array(max($a1, $a2, $a3, $a4, $a5))', 5, 'genRandReasonablyModestInteger'));

	assert(testStack('test_Phorth_Wipe', 'array()', 3, 'genRandInteger'));
	assert(testUnaryOp('test_Phorth_Push', '', 'genRandInteger'));
	assert(testStack('test_Phorth_Push2', 'array($a1)', 1, 'genRandInteger'));
	assert(testDrop('test_Phorth_Drop'));
	assert(testStack('test_Phorth_Drop2', 'array($a1)', 2, 'genRandInteger'));
	assert(testStack('test_Phorth_Nip', 'array($a2)', 2, 'genRandInteger'));
	assert(testUnaryOp('test_Phorth_Dup', '2 *', 'genRandInteger'));
	assert(testStack('test_Phorth_Dup2', 'array($a1, $a1)', 1, 'genRandInteger'));
	assert(testStack('test_Phorth_Over', 'array($a1, $a2, $a1)', 2, 'genRandInteger'));
	assert(testStack('test_Phorth_Tuck', 'array($a2, $a1, $a2)', 2, 'genRandInteger'));
	assert(testDrop('test_Phorth_Swap'));
	assert(testStack('test_Phorth_Swap2', 'array($a2, $a1)', 2, 'genRandInteger'));
	assert(testStack('test_Phorth_Rot', 'array($a2, $a3, $a1)', 3, 'genRandInteger'));
	assert(testStack('test_Phorth_Unrot', 'array($a3, $a1, $a2)', 3, 'genRandInteger'));

	assert(testStack('test_Phorth_DDrop', 'array($a1)', 3, 'genRandInteger'));
	assert(testStack('test_Phorth_DNip', 'array($a3, $a4)', 4, 'genRandInteger'));
	assert(testStack('test_Phorth_DDup', 'array($a1, $a2, $a1, $a2)', 2, 'genRandInteger'));
	assert(testStack('test_Phorth_DOver', 'array($a1, $a2, $a3, $a4, $a1, $a2)', 4, 'genRandInteger'));
	assert(testStack('test_Phorth_DTuck', 'array($a3, $a4, $a1, $a2, $a3, $a4)', 4, 'genRandInteger'));
	assert(testStack('test_Phorth_DSwap', 'array($a3, $a4, $a1, $a2)', 4, 'genRandInteger'));
	assert(testStack('test_Phorth_DRot', 'array($a3, $a4, $a5, $a6, $a1, $a2)', 6, 'genRandInteger'));
	assert(testStack('test_Phorth_DUnrot', 'array($a5, $a6, $a1, $a2, $a3, $a4)', 6, 'genRandInteger'));

	assert(testStack('test_Phorth_Pick', 'array($a1, $a2, $a3, $a4, $a5, $a2)', 5, 'genRandInteger'));
	assert(testStack('test_Phorth_Snip', 'array($a2, $a3, $a4, $a5)', 5, 'genRandInteger'));
	assert(testStack('test_Phorth_Roll', 'array($a2, $a3, $a4, $a5, $a1)', 5, 'genRandInteger'));
	assert(testStack('test_Phorth_Unroll', 'array($a5, $a1, $a2, $a3, $a4)', 5, 'genRandInteger'));

	assert(testStack('test_Phorth_Manip', 'array($a1, $a2, $a5, $a3 * 2)', 5, 'genRandInteger')); // NORX
	assert(testStack('test_Phorth_Manip2', 'array($a1, $a2, $a4, $a3, $a5 * $a5)', 5, 'genRandInteger')); // TDUW

	assert(testStack('test_Phorth_Stacks', 'array($a1, $a1 + $a2)', 2, 'genRandInteger'));
	assert(testStack('test_Phorth_Stacks2', 'array($a1, $a1 + $a6, $a2 * $a5, $a3 + $a4)', 6, 'genRandInteger'));
	assert(testStack('test_Phorth_Stacks3', 'array(($a1 * $a2) - ($a3 + $a4))', 4, 'genRandInteger'));

	assert(testStack('test_Phorth_Reduce', 'array($a3 / $a2 / $a1)', 3, 'genRandReasonablyModestInteger'));
	assert(testStack('test_Phorth_Map', 'array(! $a1, ! $a2, ! $a3)', 3, 'genRandBoolean'));
	assert(testStack('test_Phorth_ZipWith', 'array($a1 + $a6, $a2 + $a5, $a3 + $a4)', 6, 'genRandReasonablyModestInteger'));

	//assert(testStack('test_Phorth_Repeat', 'array($a1 * $a2 * $a3 * $a4)', 4, 'genRandInteger'));
	//assert(testStack('test_Phorth_Repeat2', 'array($a1 * $a2 * $a3 * $a4)', 4, 'genRandInteger'));
	assert(testStack('test_Phorth_Repeat', 'array($a1 * $a2 * $a3 * $a4)', 4, 'genRandSmallInteger'));
	assert(testStack('test_Phorth_Repeat2', 'array($a1 * $a2 * $a3 * $a4)', 4, 'genRandSmallInteger'));

	assert(testStack('test_Phorth_Sum', 'array($a1 + $a2 + $a3 + $a4 + $a5 + $a6 + $a7 + $a8 + $a9 + $a10)',
			10, 'genRandReasonablyModestInteger'));
	//assert(testStack('test_Phorth_Prod', 'array($a1 * $a2 * $a3 * $a4 * $a5 * $a6 * $a7 * $a8 * $a9 * $a10)',
	//		10, 'genRandReasonablyModestInteger'));
	assert(testStack('test_Phorth_Prod', 'array($a1 * $a2 * $a3 * $a4 * $a5 * $a6 * $a7 * $a8 * $a9 * $a10)',
			10, 'genRandSmallInteger'));

	assert(testStack('test_Phorth_Choose', 'array($a3 > $a4 ? $a1 + $a2 : $a1 - $a2)', 4, 'genRandReasonablyModestInteger'));
	assert(testStack('test_Phorth_Choose2', 'array($a3 > $a4 ? $a1 : $a2)', 4, 'genRandReasonablyModestInteger'));
	assert(testStack('test_Phorth_OnlyIf', 'array($a3 > $a4 ? $a2 : $a1)', 4, 'genRandReasonablyModestInteger'));
	assert(testStack('test_Phorth_IfThen', 'array($a3 > $a4 ? ($a1 + $a2) * 2 : ! (($a1 - $a2) / 2))',
			4, 'genRandReasonablyModestInteger'));

	assert(testStack('test_Phorth_Native', 'array(min($a1, $a2, $a3, $a4, $a5))', 5, 'genRandReasonablyModestInteger'));

	assert(testDrop('test_Phorth_Bail'));
	assert(testStack('test_Phorth_Bail2', 'array($a1)', 3, 'genRandInteger'));
	assert(testStack('test_Phorth_Bail3', 'array($a1, $a2 + 1)', 3, 'genRandInteger'));

	assert(testStack('test_Phorth_Avg', 'array(($a1 + $a2 + $a3 + $a4 + $a5 + $a6 + $a7 + $a8 + $a9 + $a10) / 10)',
			10, 'genRandReasonablyModestInteger'));

	assert(testStack('test_Phorth_Yield', 'array(666 + $a1)', 1, 'genRandInteger'));

	assert(testStack('test_Phorth_Scope', 'array($a2 + (($a1 + 1) * 4))', 2, 'genRandReasonablyModestInteger'));
	assert(testStack('test_Phorth_FuncScope', 'array((($a1 + $a2) * 2) * $a3)', 3, 'genRandReasonablyModestInteger'));

	assert(testStack('test_Phorth_Fac', 'array(fac($a1))', 1, 'genRandTinyInteger'));
	//assert(testStack('test_Phorth_Fac2', 'array(fac($a1))', 1, 'genRandSmallInteger'));
	assert(testStack('test_Phorth_Fac2', 'array(fac($a1))', 1, 'genRandTinyInteger'));

	//assert(testStack('test_Phorth_NaiveFib', 'array(fib($a1))', 1, 'genRandTinyInteger'));
	//assert(testStack('test_Phorth_NaiveFib2', 'array(fib($a1 - 1), fib($a1))', 1, 'genRandSmallInteger'));
	//assert(testStack('test_Phorth_Fib', 'array(fib($a1))', 1, 'genRandSmallInteger'));
	//assert(testStack('test_Phorth_NaiveFib2', 'array(fib($a1 - 1), fib($a1))', 1, 'genRandTinyInteger'));
	assert(testStack('test_Phorth_Fib', 'array(fib($a1))', 1, 'genRandTinyInteger'));

	Phorth::run()->Print('ALL TESTS SUCCESSFUL.'.$lnbr);

	$finished = microtime(TRUE);

	print('total execution time: '.round($finished - $started, 4).' seconds.'.$lnbr);

?>
