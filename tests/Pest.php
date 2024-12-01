<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you define here will be applied to every test case created
| during the running of your test suite. You may specify a custom
| TestCase class that will be used for all of your tests.
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');
