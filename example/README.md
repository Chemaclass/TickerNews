# EXAMPLE

In this directory you can find three different examples of how to use this api:

## Crawling

[See the working example](crawl.php)

It just crawls the website, collect the data and renders into your output terminal.

## Notifying

[See the working example](notify.php)

It crawls the data, and it sends a notification via two channels (slack + email).

## Looping

[See the working example](loop-notify.php)

It shows how can you combine these two (crawling + notifying) in an infinite loop, in order to decide (via custom policy
condition) when to trigger a notification.
