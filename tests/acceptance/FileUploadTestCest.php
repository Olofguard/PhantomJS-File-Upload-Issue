<?php

class FileUploadTestCest
{
    public function tryToTest(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('name', 'John Doe');
        $I->attachFile('file', 'testFile.csv');
        $I->click('Submit');
        $I->see('Thanks for the upload!');
    }
}