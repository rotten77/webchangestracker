# WebChangesTracker

Simple PHP app for tracking changes on websites. Sends e-mail notifications.

1. Copy files & import `install.sql`
1. Edit `config.php` and set your credentials
1. Go to app URL
1. Open Editor
1. Go to `Websites` table and click **New item**
1. Set cron for executing eah 10 minutes (`cron.php`)

## New item

* `ID` - leave out empty, will be filled automatically
* `Label` - name, label or your note
* `Status` - active/inactive
* `Tracking interval`
    * `10m` - each 10 minutes
    * `1h` - hourly
    * `1d` - daily
* `Tracking priority`
    * `schedule` - page will be tracked within scheduled jobs
    * `force_next` - page will be tracked within next cron job, then is switched back to `schedule`
* `Tracking type`
    * `single` - track only one single element on page (e.g. heading of page)
    * `multiple` - track multiple elements (e.g. blog posts)

## Tracked content

Use XPath to define tracked parts of page.

* `Block wrapper` - main wrapper of tracked block
* `Content ID` - unique part inside of wrapper which will be used as identifier for tracked record, that means that any other record with same value will be skipped
* `Content unique ID context`
    * `global` - ID is checked against all records (e.g. if you use multiple trackers for one domain)
    * `website` - ID is checked only against other website's IDs
* `Content item #` - content parts that will be used in notification message
* `Default content item #` - placeholder for missing item

**Example:**

    <div class="news">
        <span class="label">NEW!</span>
        <a href="http://www.news.com/lorem-ipsum"><h2>Lorem ipsum!</h2></a>

        <img src="http://img.news.com/lorem-ipsum.jpg" />

        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip...</p>

        <a href="http://www.news.com/lorem-ipsum">Read more</a>
    </div>

In this example, block wrapper is `<div class="news">` and unique ID can be post's URL.

|Variable|Value|Result|
|--------|-----|------|
|Block wrapper|`//div[@class="news"]`|-|
|Content ID|`/a[1]/@href`|`http://www.news.com/lorem-ipsum`|
|Content item 1|`/a[1]/h2`|`Lorem ipsum!`|
|Content item 2|`/p[1]`|`Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip...`|
|Content item 3|`/img[1]/@src`|`http://img.news.com/lorem-ipsum.jpg`|


## Message

You can pass content ID & items by `{id}`, `{1-5}` values:

    <h4><a href="{id}">{1}</a></h4>
    <p>{2}</p>
    <img src="{3}" />

<hr />
<small>Favicon by Icojam: https://www.iconfinder.com/iconsets/materia-flat-halloween-free</small>