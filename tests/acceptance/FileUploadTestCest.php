<?php

class FileUploadTestCest
{
    public function testFileUpload(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('//*[@id="name"]', 'John Doe');
        $I->attachFile('//*[@id="file"]', 'testFile.csv');
        $I->click('Submit');
        $I->see('Thanks for the upload!');
    }
}