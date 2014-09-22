# File upload issue

This example is built with Laravel and it's purpose is to recreate the file upload issue experienced when switching the browser used by codeception, from firefox, to the headless browser phantomjs. 

## Installation 
1. Clone this repo.
2. Install the latest version of PhantomJS `brew install phantomjs` 
3. Download [Selenium Standalone Server](http://docs.seleniumhq.org/download/)
> Note: I'm using selenium server standalone 2.42.2


## Steps to recreate passing tests (firefox)

1. Serve up the app `artisan serve`
2. Fire up Selenium `java -jar selenium-server-standalone-2.xx.xxx.jar`
3. Finally you're ready to run the tests `vendor/bin/codecept run`

> You should see green. `OK (1 test, 1 assertion)`



## Steps to recreate failing tests with (PhantomJS)

1. Kill the selenium server if you have it running. `ctl + c`
2. Get PhantomJS running on port 4444 `phantomjs --webdriver=4444`
3. Change the string `'firefox'` to `'phantomjs'` in the apps acceptance test yml file `tests/acceptance.suite.yml`. 
4. Finally you're ready to run the tests `vendor/bin/codecept run`

> You should see red. Tests: 1, Assertions: 0, Errors: 1.


## The issue

In short, Phantomjs is throwing an error Invalid Command Method. It appears this error is being thrown within ghostdriver, specifically in ghost driver's request_handler.js on line 105. It's as though it can't read the json from the curl request initiated by the facebook webdriver's HttpCommandExecutor class. This curl request should populate the form field for uploading the file, but instead the curl request fails after getting a status code of 405 in return. Below is an example of the curl request sent by the HttpCommandExecutor.

```json
{
  "url": "http://127.0.0.1:4444/wd/hub/session/bf4a6d70-3e71-11e4-858a-5fddebdf4faa/file",
  "content_type": "text/plain",
  "http_code": 405,
  "header_size": 99,
  "request_size": 505,
  "filetime": -1,
  "ssl_verify_result": 0,
  "redirect_count": 0,
  "total_time": 0.001383,
  "namelookup_time": 0.000016,
  "connect_time": 0.00012,
  "pretransfer_time": 0.00015,
  "size_upload": 315,
  "size_download": 844,
  "speed_download": 610267,
  "speed_upload": 227765,
  "download_content_length": 844,
  "upload_content_length": 315,
  "starttransfer_time": 0.001376,
  "redirect_time": 0,
  "certinfo": [],
  "primary_ip": "127.0.0.1",
  "primary_port": 4444,
  "local_ip": "127.0.0.1",
  "local_port": 49959,
  "redirect_url": ""
}
```

## More detail on the problem
I'm using codeception to attachFile to the form. This ultimately calls upload() on the RemoteWebElement class from the facebook web driver using the local file path. Responsibility is then handed off to the HttpCommandExecutor through an execute call execute('sendFile', $params) , where a POST curl request should be sent to attach the file to the form. From my understanding this is where phantomjs and ghostdriver take over.

I started to dig into the phantomjs/ghostdriver source, however, to me it seemed I had to rebuild phantomjs from source in order to test changes i make. I may be wrong (javascript is not my specialty), but I don't have the time to build from source over and over while debugging this issue.

It is important to note that, I can switch off phantomjs, go back to firefox as the browser for my acceptance tests, and everything works flawlessly.



