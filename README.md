# twittergraph - Readme

Some scripts to build some kind of graph based on the conversions a twitter user had in its last 200 tweets.

Example: http://knuspermagier.de/2011-striche-und-bildchen.html

It's _very poorly written_ and hacked together in a couple of hours and was more like a test of svg stuff, since I never did that before. It's also lacking some meaningful algorithm for aligning the nodes nice. 

The import script is not very optimized to work with Twitter's API limit, you need to call it several times until fetching is complete.

The whole thing is more like a proof of concept, i would be pleased if someone takes this and makes it nicer.

## 1. Requirements

1. PHP 5.1 

## 2. Usage

Create a writable file "data" and a directory "cache", call load_tweets.php and have fun.

## 3. Needed improvements

 - load_tweet.php needs to load data with less API calls
 - load_tweet.php needs to parse retweets/mentions and not @replies only
 - Better alignment of nodes
 - A lot more...
